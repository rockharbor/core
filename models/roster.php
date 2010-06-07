<?php
class Roster extends AppModel {
	var $name = 'Roster';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	
	// this is mainly here to fail empty roster saves
	var $validate = array(
		'roster_status_id' => array(
			'rule' => 'notEmpty',
			'required' => true
		)
	);
	
	var $actsAs = array(
		'Logable',
		'Containable'
	);
	
	var $virtualFields = array(
		'amount_due' => '@vad:=(SELECT (IF (Roster.parent_id IS NOT NULL, ad.childcare, ad.total)) FROM payment_options as ad WHERE ad.id = Roster.payment_option_id)',
		'amount_paid' => '@vap:=(COALESCE((SELECT SUM(ap.amount) FROM payments as ap WHERE ap.roster_id = Roster.id), 0))',
		'balance' => '@vad-@vap'
	);
	
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Involvement' => array(
			'className' => 'Involvement',
			'foreignKey' => 'involvement_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Role' => array(
			'className' => 'Role',
			'foreignKey' => 'role_id',
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
		'Parent' => array(
			'className' => 'User',
			'foreignKey' => 'parent_id'
		),
		'RosterStatus'
	);

	var $hasMany = array(
		'Answer' => array(
			'className' => 'Answer',
			'foreignKey' => 'roster_id',
			'dependent' => true
		),
		'Payment' => array(
			'className' => 'Payment',
			'foreignKey' => 'roster_id',
			'dependent' => false
		)
	);


}
?>