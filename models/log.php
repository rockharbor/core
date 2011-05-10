<?php
/**
 * Log model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Log model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Log extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Log';

/**
 * The field to use when generating list
 *
 * @var string
 */
	var $displayField = 'title';

/**
 * Default order
 *
 * @var string
 */
	var $order = 'Log.created DESC';

/**
 * BelongsTo association link
 *
 * @var array
 */
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		)
	);
}
?>