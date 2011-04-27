<?php
/**
 * App model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app
 */

/**
 * App model
 *
 * All models should extend this class
 *
 * @package       core
 * @subpackage    core.app
 */
class AppModel extends Model {

/**
 * Default recursive property
 *
 * @var integer
 */
	var $recursive = -1;

/**
 * Behaviors to attach
 *
 * @var array
 */
	var $actsAs = array(
		'Sanitizer.Sanitize' => array(
			'decodeHtml' => true
		),
		'Cacher.Cache'
	);

/**
 * Extends model construction
 * 
 * ### Extended functionality:
 * - allows use of :ALIAS: in virtual field definitions to be replaced with the
 *		model's alias
 *
 * @param mixed $id Sets the model's id on startup
 * @param string $table The name of the database table to use
 * @param string $ds The datasource connection name
 * @see Model::__construct()
 */
	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		foreach ($this->virtualFields as &$virtualField) {
			$virtualField = String::insert(
				$virtualField,
				array(
					'ALIAS' => $this->alias,
				),
				array(
					'after' => ':'
				)
			);
		}
	}

/**
 * Takes a set of generic conditions and scopes them according to this model. If
 * a model is defined in the condition, it will keep that model's scope. Only
 * creates conditions if the model has the field.
 *
 * {{{
 * // assuming we're on the user model
 * array('active' => false, 'Ministry.active' => true, 'nonexistentfield' => 'val');
 * // becomes
 * array('User.active' => false, 'Ministry.active' => true);
 * }}}
 *
 * @param array $conditions Generic conditions
 */
	function scopeConditions($conditions = array()) {
		$scoped = array();
		foreach ($conditions as $field => $value) {
			$exp = explode('.', $field);
			$model = $exp[0];
			if (!isset($exp[1])) {
				$field = $model;
				$model = $this->alias;
			} else {
				$field = $exp[1];
			}
			if ($model == $this->alias && !$this->hasField($field, true)) {
				continue;
			}
			$scoped[$model.'.'.$field] = $value;
		}
		return $scoped;
	}

/**
 * Sets the default image based on the results of a find. Only sets the default
 * image if:
 * - An image was included in the find conditions (or contained)
 * - A default image app setting key exists
 * - The default image app setting is actually set
 *
 * Sets the 'ImageIcon' key if
 * - One is not set
 * - If the model has an image, that will be used as the icon. If not and a
 *	  default one exists, the default will be used.
 *
 * @param array $results The results of a find
 * @return array (Perhaps) modified results
 */
	function defaultImage($results) {
		$offset = false;
		if (isset($results['Image'])) {
			$offset =& $results;
		} elseif (isset($results[$this->alias]['Image'])) {
			$offset =& $results[$this->alias];
		}
		if ($offset) {
			if (empty($offset['Image']) || isset($offset['Image']['id']) && empty($offset['Image']['id'])) {
				$default = Core::read(strtolower(Inflector::pluralize($this->alias)).'.default_image');
				$icon = Core::read(strtolower(Inflector::pluralize($this->alias)).'.default_icon');
				if (!$icon) {
					$icon = $default;
				}
				if ($default && isset($default['id'])) {
					if (isset($this->hasOne['Image'])) {
						$offset['Image'] = $default;
						$offset['ImageIcon'] = $icon;
					} elseif (isset($this->hasMany['Image'])) {
						$offset['Image'] = array($default);
						$offset['ImageIcon'] = $icon;
					}
				}
			} else {
				if (isset($this->hasOne['Image'])) {
					$offset['ImageIcon'] = $offset['Image'];
				} elseif (isset($this->hasMany['Image'])) {
					$offset['ImageIcon'] = $offset['Image'][0];
				}
			}
		}
		return $results;
	}

/**
 * Creates a MATCH (...) AGAINST (...) expression from the query using the fields
 * defined in filterArgs
 *
 * @param array $data The key value pair for the filterArg's name to the query
 * @return string
 */
	function makeFulltext($data = array()) {
		$filterName = key($data);
		$filter = Set::extract('/.[name='.$filterName.']', $this->filterArgs);
		if (!isset($filter[0]['field'])) {
			$filter[0]['field'] = $this->alias.'.'.$this->displayField;
		}
		$field = $filter[0]['field'];
		$query = $data[$filterName];
		$ds = ConnectionManager::getDataSource($this->useDbConfig);
		if (!is_array($field)) {
			$field = array($field);
		}
		return array($ds->expression('MATCH ('.implode(',',array_map(array($ds, 'name'), $field)).') AGAINST ('.$ds->value($query).' IN BOOLEAN MODE)'));
	}

/**
 * Creates a LIKE '%foo%' AND LIKE '%bar%' statement as defined in filterArgs
 *
 * ### Options:
 * - including a `operator` key in the filterArg will change the operator (AND
 *   or OR)
 *
 * @param array $data The key value pair for the filterArg's name to the query
 * @return string
 */
	function makeLikeConditions($data = array()) {
		$filterName = key($data);
		$filter = Set::extract('/.[name='.$filterName.']', $this->filterArgs);
		if (!isset($filter[0]['field'])) {
			$filter[0]['field'] = $this->alias.'.'.$this->displayField;
		}
		if (!isset($filter[0]['operator'])) {
			$filter[0]['operator'] = 'AND';
		}
		$field = $filter[0]['field'];
		$operator = $filter[0]['operator'];
		$query = $data[$filterName];
		if (!is_array($field)) {
			$field = array($field);
		}
		$conditions = array();
		foreach ($field as $val) {
			$conditions[$val.' LIKE'] = $query.'%';
		}
		if (strtoupper($operator) === 'AND') {
			return $conditions;
		} else {
			return array(
				$operator => $conditions
			);
		}
	}

/**
 * Creates a simplistic `contain` array from post data
 *
 * Use in conjunction with Controller::postConditions() to make search forms super-quick!
 *
 * @param array $data The Cake post data
 * @param array $associated Associated models (used during recursion)
 * @return array The contain array
 * @access public
 */
	function postContains($data, $Model = null) {
		// get associated models
		if (!$Model) {
			$Model = $this;
		}
		$associated = $Model->getAssociated();

		// clear out all post conditions
		foreach ($data as $model => $field) {
			if (!array_key_exists($model, $associated)) {
				// remove if it's not directly associated
				unset($data[$model]);
			} else {
				// check for habtm [Publication][Publication][0] = 1
				if ($model == array_shift(array_keys($field))) {
					$field = array();
				}
				// recusively check for more models to contain
				$data[$model] = $this->postContains($field, $Model->{$model});
			}
		}
		
		// don't let contain reference itself
		unset($data[$this->name]);
		unset($data[$this->alias]);
		
		return $data;
	}

/**
 * Checks if a user owns a record
 *
 * @param integer $userId The user's id
 * @param integer $modelId The model id. By default it uses Model::id.
 * @return boolean
 * @access public
 */
	function ownedBy($userId = null, $modelId = null) {		
		if (!$this->id || $modelId) {
			$this->id = $modelId;
		}		
		
		if (!$userId || !$this->id) {
			return false;
		}

		$class = $field = null;

		// get the field the user id might be in
		$fields = array('user_id', 'model_id', 'foreign_key', 'id', 'payment_placed_by');
		$field = 'user_id';
		$f = 0;
		while (!$this->hasField($field) && $f < count($fields)) {
			$field = $fields[$f];
			$f++;
		}
		
		// check for class/model field if it's a polymorphic model
		if (!in_array($field, array('id', 'user_id'))) {
			$fields = array('model', 'class');
			foreach ($fields as $classField) {
				if ($this->hasField($classField)) {
					$class = $classField;
					break;
				}
			}

			return $this->hasAny(array(
				$field => $userId,
				$class => 'User'
			));
		}
		
		return $this->field($field) == $userId;
	}

/**
 * Toggles the `active` field
 *
 * @param integer $id Id of model
 * @param boolean $active Whether to make the model inactive or active
 * @param boolean $recursive Whether to iterate through the model's relationships and mark them as $active
 * @return boolean Success
 */
	function toggleActivity($id = null, $active = false, $recursive = false) {
		if (!$id) {
			return false;
		}

		$this->id = $id;
		$this->recursive = 1;
		$data = $this->read(null, $id);
				
		if ($recursive) {
			foreach ($this->hasOne as $hasOne => $config) {
				// get id
				$hasOneId = $data[$hasOne][$this->{$hasOne}->primaryKey];
				// only go one level deep because most everything leads back to user
				if ($id) {
					$this->{$hasOne}->toggleActivity($hasOneId, $active, false);
				}
			}
			
			foreach ($this->hasMany as $hasMany => $config) {
				// get ids
				$hasManyIds = Set::extract('/'.$hasMany.'/'.$this->{$hasMany}->primaryKey, $data);
				// only go one level deep because most everything leads back to user
				foreach ($hasManyIds as $disableId) {
					$this->{$hasMany}->toggleActivity($disableId, $active, false);
				}
			}
		}
			
		if ($this->hasField('active')) {
			return $this->saveField('active', $active) ? true : false;
		} else {
			return false;
		}
	}
	
/**
 *  Validation rule that checks to see if the passed 
 *  field matches another field 
 * (useful for password / password confirmation)
 *
 * @param array $data Passed by validator
 * @param string $compareField Field to compare it to
 * @return boolean True if it passes validation
 */	
	function identicalFieldValues(&$data, $compareField) {
		// $data array is passed using the form field name as the key
		// have to extract the value to make the function generic
		$value = array_values($data);
		$comparewithvalue = $value[0];	
		
		return ($this->data[$this->name][$compareField] == $comparewithvalue);
	}

/**
 * Deconstructs complex data (specifically here, date) and creates a partial
 * date from a Cake date array
 *
 * The Form helper in Cake splits dates into 3 pieces: month, day and year.
 * If the column in the database is set to a string, we'll allow a "partial
 * date" so users can, say, estimate the time they were baptized.
 *
 * @param string $field The name of the column
 * @param array $value The complex value being saved
 * @return mixed A string if it should be a date string, or deconstructed data
 *		as determined by Model::deconstruct()
 * @see Model::deconstruct()
 * @see FormHelper::dateTime()
 */
	function deconstruct($field, $value) {
		if ($this->getColumnType($field) == 'string') {
			if (isset($value['month']) && empty($value['month'])) {
				$value['month'] = '00';
			}
			if (isset($value['day']) && empty($value['day'])) {
				$value['day'] = '00';
			}
			if (isset($value['year']) && empty($value['year'])) {
				$value['year'] = '0000';
			}
			
			// convert to proper type
			if (array_key_exists('month', $value) && array_key_exists('day', $value) && array_key_exists('year', $value)) {
				$value = $value['year'].'-'.$value['month'].'-'.$value['day'];
			}
		}
		return parent::deconstruct($field, $value);
	}

}
?>