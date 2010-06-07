<?php
class PaymentOption extends AppModel {
	var $name = 'PaymentOption';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Involvement' => array(
			'className' => 'Involvement',
			'foreignKey' => 'involvement_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	var $validate = array(
		'total' => array(
			'money' => array(	
				'rule' => 'money',
				'message' => 'Please enter a monetary amount.'
			),
			'notEmpty'
		),
		'account_code' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'required' => true
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
				'message' => 'Please enter a monetary amount.',
				'required' => false,
				'allowEmpty' => true
			)
		),
		'deposit' => array(
			'money' => array(	
				'rule' => 'money',
				'message' => 'Please enter a monetary amount.',
				'required' => false,
				'allowEmpty' => true
			)
		)
	);

}
?>