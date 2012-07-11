<?php
/**
 * Payment model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Payment model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Payment extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Payment';

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	var $actsAs = array(
		'Containable'
	);

/**
 * Validation rules
 *
 * @var array
 */
	var $validate = array(
		'amount' => array(
			'rule' => 'notEmpty',
			'required' => false,
			'message' => 'Please enter a valid amount.'
		)
	);

/**
 * BelongsTo association link
 *
 * @var array
 */
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		),
		'Roster' => array(
			'className' => 'Roster',
			'foreignKey' => 'roster_id'
		),	
		'Payer' => array(
			'className' => 'User',
			'foreignKey' => 'payment_placed_by'
		),		
		'PaymentType'
	);
}
?>