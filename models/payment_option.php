<?php
/**
 * Payment option model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * PaymentOption model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class PaymentOption extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'PaymentOption';

/**
 * BelongsTo association link
 *
 * @var array
 */
	var $belongsTo = array(
		'Involvement' => array(
			'className' => 'Involvement',
			'foreignKey' => 'involvement_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * Validation rules
 *
 * @var array
 */
	var $validate = array(
		'total' => array(
			'money' => array(	
				'rule' => 'money',
				'message' => 'Please enter a valid amount.'
			),
			'notEmpty'
		),
		'account_code' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => 'Please fill in the required field.'
			)
		),
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'required' => true
			)
		),
		'childcare' => array(
			'money' => array(	
				'rule' => 'money',
				'message' => 'Please enter a valid amount.',
				'required' => false,
				'allowEmpty' => true
			)
		),
		'deposit' => array(
			'money' => array(	
				'rule' => 'money',
				'message' => 'Please enter a valid amount.',
				'required' => false,
				'allowEmpty' => true
			)
		)
	);

}
