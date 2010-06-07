<?php
class Payment extends AppModel {
	var $name = 'Payment';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	
	var $actsAs = array(
		'Containable'
	);
	
	var $validate = array(
		'user_id' => 'notEmpty',
		'amount' => array(
			'rule' => 'notEmpty',
			'required' => true
		),
		'payment_type_id' => 'notEmpty',
		'payment_placed_by' => 'notEmpty'
	);
	
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Roster' => array(
			'className' => 'Roster',
			'foreignKey' => 'roster_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),		
		'PaymentOption' => array(
			'className' => 'PaymentOption',
			'foreignKey' => 'payment_option_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),		
		'Payer' => array(
			'className' => 'User',
			'foreignKey' => 'payment_placed_by',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),		
		'PaymentType'
	);
}
?>