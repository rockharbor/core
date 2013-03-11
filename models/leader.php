<?php
/**
 * Leader model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Leader model
 *
 * Polymorphic model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Leader extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	public $name = 'Leader';

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
		'User',
		'Campus' => array(
			'foreignKey' => 'model_id'
		),
		'Ministry' => array(
			'foreignKey' => 'model_id'
		),
		'Involvement' => array(
			'foreignKey' => 'model_id'
		)
	);
}
