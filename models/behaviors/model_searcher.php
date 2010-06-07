<?php
/**
 * ModelSearcher Behavior
 *
 * Allows searching all model relationships (hasMany and HABTM) by searching those models
 * and pulling their foreign keys to place in this query's primary key
 *
 * @author Jeremy Harris <jharris@rockharbor.org>
 */
class ModelSearcherBehavior extends ModelBehavior {

/**
 * Stored list of models to narrow PK's by
 *
 * @var array
 * @access private
 */
	var $_searchModels = array();

/**
 * ModelBehavior::beforeFind() callback
 *
 * Modifies the find conditions to remove hasMany and HABTM models from the original
 * set of conditions, and insteads searches those models based on the conditions sent
 * and adds them to a list of ids to filter this model's primary key by
 *
 * @param object $Model The referencing model
 * @param array $query The query sent by the model
 * @return mixed True to continue with the original query, or the modified query
 * @access public
 */
	function beforeFind(&$Model, &$query) {		
		// new search
		$this->_searchModels = array();
		
		if (!isset($query['conditions'])) {
			return true;
		}
		
		// because containable wipes the associations (according to what's needed)
		// we need to instantiate the model to check associations it _should_ have
		App::import('Model', $Model->name);
		$_Model = new $Model->name;
		
		// remove conditions that would break sql
		$this->_modifyConditions($_Model, $query['conditions']);
		return $query;
	}
	

/**
 * Modifies conditions by removing those that would cause SQL to fail from lack of joins
 * and replacing them with a conditions to find the primary key's ids from a search on that table 
 *
 * @param object $Model The referencing model
 * @param array $conditions Cake conditions
 * @access private
 */ 
	function _modifyConditions(&$Model, &$conditions, $bool = 'AND') {
		foreach ($conditions as $key => &$value) {			
			// extract model name, if any
			list($model) = explode('.', $key);
			
			// get association, if any
			$associated = $this->isAssociated($Model, $model);
			
			// check to see if this is a deeper conditions set
			if (is_array($value) && !$associated) {
				$this->_modifyConditions($Model, $value, $model);
			} elseif (in_array($associated, array('hasMany', 'hasAndBelongsToMany'))) {
				switch ($associated) {
					case 'hasMany':
						$fkModel = $model;
						$origKey = $key;
					break;
					case 'hasAndBelongsToMany':
						$tables = array($model, $Model->name);
						sort($tables);
						$joinTable = isset($Model->{$associated}[$model]['joinTable']) ? $Model->{$associated}[$model]['joinTable'] : implode('_', $tables);
						$afk = isset($Model->{$associated}[$model]['associationForeignKey']) ? $Model->{$associated}[$model]['associationForeignKey'] : $Model->{$model}->primaryKey;
						$fkModel = Inflector::classify($joinTable);
						$origKey = $key;
						$key = $fkModel.'.'.$afk;
					break;
				}
				
				$fk = isset($Model->{$associated}[$model]['foreignKey']) ? $Model->{$associated}[$model]['foreignKey'] : strtolower($Model->name).'_id';
								
				$data = $Model->{$fkModel}->find('all', array(
					'fields' => array(
						$fk
					),
					'conditions' => array(
						$key => $value
					),
					'recursive' => -1
				));
				
				$ids = Set::extract('/'.$Model->alias.'/'.$Model->primaryKey, $data);
				// since we only got the fk, flatten will let use get the value regardless of the fk
				$ids = array_merge(array_values(Set::flatten($data)), $ids);
				
				$conditions[$Model->alias.'.'.$Model->primaryKey] = $ids;	
				
				// replace these conditions with a search on the pk
				unset($conditions[$origKey]);
			}
		}
	}
	

/**
 * Checks to see if a model is associated with the current model
 *
 * @param object $Model The referencing model
 * @param string $model Model name
 * @param array $checkAssociations Associations to check for. Default is all
 * @return mixed First association name if found, false if no matches
 * @access public
 */ 
	function isAssociated(&$Model, $model = null, $checkAssociations = array()) {
		if (empty($checkAssociations)) {
			$checkAssociations = array(
				'belongsTo',
				'hasOne',
				'hasMany',
				'hasAndBelongsToMany'
			);
		}
		
		foreach ($checkAssociations as $assoc) {
			if (isset($Model->{$assoc}[$model])) {
				return $assoc;
			}
		}
		
		return false;
	}
}

?>