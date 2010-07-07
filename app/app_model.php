<?php
/* SVN FILE: $Id$ */
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.model
 * @since         CakePHP(tm) v 0.2.9
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Application model for Cake.
 *
 * This is a placeholder class.
 * Create the same file in app/app_model.php
 * Add your application-wide methods to the class, your models will inherit them.
 *
 * @package       cake
 * @subpackage    cake.cake.libs.model
 */
class AppModel extends Model {

	var $recursive = -1;

/**
 * Creates a simplistic `contain` array from post data
 *
 * Use in conjunction with Controller::postConditions() to make search forms super-quick!
 *
 * @param array $data The Cake post data
 * @return array The contain array
 * @access public
 */
	function postContains($data) {
		// get assoociated models
		$associated = $this->getAssociated();

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
				$data[$model] = $this->postContains($field);
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
		$fields = array('user_id', 'model_id', 'foreign_key', 'id');
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
 * Runs before saving model data. 
 *
 * #### Extra functionality:
 * - Allow partial dates to be saved
 *
 * @return boolean Feel free to save the model, Cake!
 */
	function beforeSave() {
		if (!empty($this->data)) {			
			if (isset($this->data[$this->alias][0])) {
				// hasmany?
				foreach ($this->data[$this->alias] as &$modelSave) {
					foreach ($modelSave as $field => &$value) {
						$value = $this->_createPartialDates($field, $value);
					}
				}
			} else {
				// has one
				foreach ($this->data[$this->alias] as $field => &$value) {
					$value = $this->_createPartialDates($field, $value);
				}
			}		
		}
		
		return true;
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
			return $this->saveField('active', $active);
		} else {
			return true;
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
 * Creates a partial date from a Cake date value
 *
 * The Form helper in Cake splits dates into 3 pieces: month, day and year.
 * If the column in the database is set to a string, we'll allow a "partial
 * date" so users can, say, estimate the time they were baptized.
 *
 * @param string $field The name of the column
 * @param mixed $value The value being saved. An array or string.
 * @return mixed Either the original value or the modified partial one.
 */
	function _createPartialDates($field, $value) {
		// checks for date inputs that are being placed in string columns
		// dates put in date or datetime cols are strict
		if (is_array($value) && $this->getColumnType($field) == 'string') {
			// replace empty values with 0's instead
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
		
		return $value;
	}

}
?>