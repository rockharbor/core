<?php
/**
 * Roster model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Roster model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Roster extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Roster';
	

/**
 * Validation rules
 *
 * This validation rule is here to prevent empty roster saves
 *
 * @var array
 */
	var $validate = array(
		'roster_status_id' => array(
			'rule' => 'notEmpty',
			'required' => true
		)
	);

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	var $actsAs = array(
		'Logable',
		'Containable'
	);

/**
 * Virtual field definitions
 *
 * @var array
 */
	var $virtualFields = array(
		'amount_due' => '@vad:=(SELECT (IF (Roster.parent_id IS NOT NULL, ad.childcare, ad.total)) FROM payment_options as ad WHERE ad.id = Roster.payment_option_id)',
		'amount_paid' => '@vap:=(COALESCE((SELECT SUM(ap.amount) FROM payments as ap WHERE ap.roster_id = Roster.id), 0))',
		'balance' => '@vad-@vap'
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
		'Involvement' => array(
			'className' => 'Involvement',
			'foreignKey' => 'involvement_id'
		),
		'Role' => array(
			'className' => 'Role',
			'foreignKey' => 'role_id'
		),
		'PaymentOption' => array(
			'className' => 'PaymentOption',
			'foreignKey' => 'payment_option_id'
		),
		'Parent' => array(
			'className' => 'User',
			'foreignKey' => 'parent_id'
		),
		'RosterStatus'
	);

/**
 * HasMany association link
 *
 * @var array
 */
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