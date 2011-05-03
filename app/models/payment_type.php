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
	var $name = 'PaymentType';

/**
 * HasMany association link
 *
 * @var array
 */
	var $hasMany = array(
		'Payment'
	);

/**
 * Hardcoded payment types. The PaymentType model is used for defining types
 * within these types, i.e., Visa would have type=0
 * 
 * @var array
 */
	var $types = array(
		0 => 'Credit Card',
		1 => 'Cash',
		2 => 'Check'
	);

}
?>