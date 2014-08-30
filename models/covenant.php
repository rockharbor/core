<?php
/**
 * Covenant model class.
 *
 * @copyright     Copyright 2014, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Covenant model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Covenant extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	public $name = 'Covenant';

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	public $actsAs = array(
		'Containable'
	);

/**
 * BelongsTo association link
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'foreign_key' => 'user_id'
		)
	);
}
