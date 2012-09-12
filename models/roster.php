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
 * Extra behaviors for this model
 *
 * @var array
 */
	var $actsAs = array(
		'Containable',
		'Search.Searchable',
		'Cacher.Cache',
		'Linkable.AdvancedLinkable',
		'Logable'
	);

/**
 * Virtual field definitions
 *
 * @var array
 */
	var $virtualFields = array(
		'amount_due' => '@vad:=CAST((SELECT (IF (:ALIAS:.parent_id IS NOT NULL, ad.childcare, ad.total)) FROM payment_options as ad WHERE ad.id = :ALIAS:.payment_option_id) AS DECIMAL(10,2))',
		'amount_paid' => '@vap:=CAST((COALESCE((SELECT SUM(ap.amount) FROM payments as ap WHERE ap.roster_id = :ALIAS:.id), 0)) AS DECIMAL(10,2))',
		'balance' => 'CAST(@vad-@vap AS DECIMAL(10,2))'
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

/**
 * HasAndBelongsToMany association link
 *
 * @var array
 */
	var $hasAndBelongsToMany = array(
		'Role' => array(
			'className' => 'Role',
			'foreignKey' => 'roster_id',
			'associationForeignKey' => 'role_id',
			'dependent' => true,
		),
	);

/**
 * Filter args for the Search.Searchable behavior
 *
 * @var array
 * @see Search.Searchable::parseCriteria()
 */
	var $filterArgs = array(
		array(
			'name' => 'roles',
			'type' => 'subquery',
			'method' => 'findByRoles',
			'field' => 'Roster.id'
		)
	);
	
/**
 * Remove joins from counts
 * 
 * @param array $conditions Array of find conditions
 * @param array $recursive Recursive setting
 * @param array $extra Extra find options
 * @return integer Record count
 */
	function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		if (isset($extra['link'])) {
			$link = $extra['link'];
		}
		return $this->find('count', compact('conditions', 'link'));
	}

/**
 * Generates a query for finding HABTM Role data
 *
 * @param array $data Search data
 * @return array Query
 */
	function findByRoles($data = array()) {
		$this->RolesRoster->bindModel(array('belongsTo' => array('Role', 'Roster')));
		$this->RolesRoster->Behaviors->attach('Containable', array('autoFields' => false));
		$this->RolesRoster->Behaviors->attach('Search.Searchable');
		$existsQuery = array();
		foreach ($data['roles'] as $role) {
			$query = 'SELECT 1 FROM '.Inflector::tableize($this->RolesRoster->alias).' WHERE role_id = '.$role.' AND roster_id = RolesRoster.roster_id';
			$existsQuery[] = 'EXISTS ('.$query.')';
		}
		$query = $this->RolesRoster->getQuery('all', array(
			'conditions' => implode(' AND ', $existsQuery),
			'fields' => array('roster_id'),
			'contain' => array('Role')
		));
		return $query;
	}

/**
 * Adds necessary information to a new roster record.
 *
 * ### Options:
 * - `roster` The Roster model data
 * - `defaults` The default data, (see list below)
 * - `involvement` The Involvement model data
 * - `creditCard` The CreditCard model data (if any)
 * - `payer` The User data for the person paying (if any)
 * - `parent` The parent (if childcare)
 *
 * ### Defaults:
 * - `payment_option_id` The PaymentOption id. If none is supplied, the first one
 *   will be chosen automatically
 * - `payment_type_id` The PaymentType id
 * - `pay_later` Whether they chose to pay now or later
 * - `pay_deposit_amount` If they chose the payment deposit amount instead of total
 *
 * @param array $options List of information used to change the roster record
 * @return array New roster record
 *
 */
	function setDefaultData($options) {
		$_options = array(
			'creditCard' => array(),
			'defaults' => array(),
			'parent' => null
		);
		$options = array_merge($_options, $options);
		$_defaults = array(
			'payment_option_id' => null,
			'payment_type_id' => null,
			'pay_later' => false,
			'pay_deposit_amount' => false,
		);
		$options['defaults'] = array_merge($_defaults, $options['defaults']);
		
		extract($options);
		
		if (empty($defaults['payment_option_id']) && $involvement['Involvement']['take_payment']) {
			$firstPaymentOption = $this->PaymentOption->find('first', array(
				'fields' => array(
					'id'
				),
				'conditions' => array(
					'involvement_id' => $involvement['Involvement']['id']
				)
			));
			$defaults['payment_option_id'] = $firstPaymentOption['PaymentOption']['id'];
		}
		
		// set defaults
		$roster['Roster']['involvement_id'] = $involvement['Involvement']['id'];
		$roster['Roster']['roster_status_id'] = $involvement['Involvement']['default_status_id'];
		$roster['Roster']['parent_id'] = $parent;
		$roster['Roster']['payment_option_id'] = $defaults['payment_option_id'];

		$exists = $this->find('first', array(
			'fields' => array(
				'Roster.id'
			),
			'conditions' => array(
				'Roster.involvement_id' => $involvement['Involvement']['id'],
				'Roster.user_id' => $roster['Roster']['user_id']
			)
		));
		if (!empty($exists)) {
			// this will confirm them
			$roster['Roster']['id'] = $exists['Roster']['id'];
			$roster['Roster']['roster_status_id'] = 1;
		}
		
		// only add a payment if we're taking one
		if ($involvement['Involvement']['take_payment'] && $defaults['payment_option_id'] > 0 && !$defaults['pay_later']) {
			if (empty($defaults['payment_option_id'])) {
				$paymentOption = $this->PaymentOption->find('first', array(
					'conditions' => array(
						'involvement_id' => $involvement['Involvement']['id']
					)
				));
			} else {
				$paymentOption = $this->PaymentOption->read(null, $defaults['payment_option_id']);
			}
			
			$paymentType = $this->Payment->PaymentType->read(null, $defaults['payment_type_id']);
			
			if (is_null($parent)) {
				$amount = $defaults['pay_deposit_amount'] ? $paymentOption['PaymentOption']['deposit'] : $paymentOption['PaymentOption']['total'];
			} else {
				$amount = $paymentOption['PaymentOption']['childcare'];
			}

			if ($amount > 0) {
				// add payment record to be saved (transaction id to be added later)
				$roster['Payment'] = array(
					'0' => array(
						'user_id' => $roster['Roster']['user_id'],
						'amount' => $amount,
						'payment_type_id' => $paymentType['PaymentType']['id'],
						'number' => substr($creditCard['CreditCard']['credit_card_number'], -4),
						'payment_placed_by' => $payer['User']['id'],
						'payment_option_id' => $paymentOption['PaymentOption']['id'],
						'comment' => $creditCard['CreditCard']['first_name'].' '.$creditCard['CreditCard']['last_name'].'\'s card processed by '.$payer['Profile']['name'].'.'
					)
				);
			}
		}

		return $roster;
	}
}
