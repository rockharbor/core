<?php
/**
 * Notification model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Notification model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Notification extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Notification';

/**
 * BelongsTo association link
 *
 * @var array
 */
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * Types of notifications
 *
 * @var array
 */
	var $types = array(
		'alert' => 'Alert',
		'invitation' => 'Invitation',
		'default' => 'Notification'
	);
}
?>