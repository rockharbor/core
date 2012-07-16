<?php
/**
 * Role model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Role model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Role extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Role';
	
/**
 * Default order
 * 
 * @var string
 */
	var $order = ':ALIAS:.name ASC';

/**
 * Validation rules
 *
 * @var array
 */
	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'Please fill in the required field.'
		)
	);

/**
 * BelongsTo association link
 *
 * @var array
 */
	var $belongsTo = array(
		'Ministry' => array(
			'className' => 'Ministry',
			'foreignKey' => 'ministry_id'
		)
	);

/**
 * HasAndBelongsToMany association link
 *
 * @var array
 */
	var $hasAndBelongsToMany = array(
		'Roster' => array(
			'className' => 'Roster',
			'foreignKey' => 'role_id',
			'associationForeignKey' => 'roster_id',
			'dependent' => true,
		),
	);
	
/**
 * Gets all roles for a ministry and the roles of its parent ministry
 * 
 * @param integer $ministryId The ministry id
 * @return array Array of role ids
 * @todo Make it get not just the immediate parent, but all parents
 */
	function findRoles($ministryId = null) {
		if ($ministryId === null) {
			return array();
		}
		$parent = $this->Ministry->read(array('parent_id'), $ministryId);
		if (!empty($parent['Ministry']['parent_id'])) {
			$ministryId = array(
				$parent['Ministry']['parent_id'],
				$ministryId
			);
		}
		$roles = $this->find('all', array(
			'fields' => array(
				'id'
			),
			'conditions' => array(
				'ministry_id' => $ministryId
			)
		));
		return Set::extract('/Role/id', $roles);
	}
}
