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
	var $order = 'Role.name ASC';

/**
 * Validation rules
 *
 * @var array
 */
	var $validate = array(
		'name' => array(
			'notEmpty'
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
}
?>