<?php
/**
 * Involvement type class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * InvolvementType model
 *
 * @package       core
 * @subpackage    core.app.models
 * @todo Move into Involvement model as a variable instead
 */
class InvolvementType extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	public $name = 'InvolvementType';

/**
 * Default order
 *
 * @var string
 */
	public $order = ':ALIAS:.name ASC';

/**
 * HasMany association link
 *
 * @var array
 */
	public $hasMany = array(
		'Involvement' => array(
			'className' => 'Involvement',
			'foreignKey' => 'involvement_type_id',
			'dependent' => false
		)
	);

}
