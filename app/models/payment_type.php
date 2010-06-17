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

}
?>