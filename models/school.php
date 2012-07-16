<?php
/**
 * School model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * School model
 *
 * This is a polymorphic model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class School extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'School';
	
/**
 * Default order
 *
 * @var string
 */
	var $order = ':ALIAS:.name ASC';

/**
 * Types of schools
 *
 * @var array
 */
	var $types = array(
		'e' => 'Elementary School',
		'm' => 'Middle School',
		'h' => 'High School',
		'c' => 'College'
	);

/**
 * Model::beforeFind() callback
 *
 * Checks to see if an alias version of School is running a find. If so, it will
 * automatically limit to that particular type. If you don't want it to limit by
 * type just use the actual School model instead.
 *
 * @param array $queryData The query data
 * @return array The modified query data
 */
	function beforeFind($queryData = array()) {
		$types = array_map(array('Inflector', 'classify'), $this->types);		

		if ($this->alias != $this->name && in_array($this->alias, $types)) {
			$types = array_flip($types);
			$conditions = array(
				'conditions' => array(
					$this->alias.'.type' => $types[$this->alias]
				)
			);
			$queryData = Set::merge($queryData, $conditions);
		}

		return $queryData;
	}
}
