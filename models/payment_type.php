<?php
/**
 * Payment type model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * PaymentType model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class PaymentType extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	public $name = 'PaymentType';

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
		'Payment'
	);

/**
 * Hardcoded payment types. The PaymentType model is used for defining types
 * within these types, i.e., Visa would have type=0
 *
 * @var array
 */
	public $types = array(
		0 => 'Credit Card',
		1 => 'Cash',
		2 => 'Check'
	);

/**
 * HasOne association link
 *
 * @var array
 */
	public $belongsTo = array(
		'Group'
	);

}
