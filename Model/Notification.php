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
	public $name = 'Notification';

/**
 * BelongsTo association link
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * Sanitization rules
 *
 * @var array
 * @see Sanitizer.SanitizeBehavior
 */
	public $sanitize = array(
		'body' => 'stripScripts'
	);
}
