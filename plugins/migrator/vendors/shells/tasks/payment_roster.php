<?php

class PaymentRosterTask extends MigratorTask {

	var $_oldPkMapping = array(
		'event_id' => array('events' => 'Involvement'),
		'event_roster_id' => array('event_roster' => 'Roster'),
		'person_id' => array('person' => 'User'),
	);

	var $_oldTable = 'event_roster';
	var $_oldPk = 'event_roster_id';
	var $_newModel = 'Payment';

	var $addLinkages = false;

	function findData($limit = null) {
		$options = array(
			'order' => $this->_oldPk,
			'conditions' => array(
				'Model.amountPaid >' => 0
			)
		);
		
		// needed for bindmodel
		$this->old->primaryKey = $this->_oldPk;
		
		// save this model in class registry since table name/keys are wonky
		new Model(array(
			'table' => 'credit_card_payment',
			'ds' => $this->_oldDbConfig,
			'name' => 'CreditCardPayment'
		));
		// bind credit card model
		$this->old->bindModel(array(
			'hasMany' => array(
				'CreditCardPayment' => array(
					'className' => 'CreditCardPayment',
					'table' => 'credit_card_payment',
					'foreignKey' => 'event_roster_id'
				)
			)
		));
		
		if ($limit) {
			$options['limit'] = $limit;
		}
		$this->old->recursive = 1;
		return $this->old->find('all', $options);
	}
	
/**
 * Checks to see if this roster has a credit card payment associated with it. If not,
 * it will add a payment if there is an amount paid
 * 
 * @param string $amount 
 */
	function _prepareAmountPaid($amount) {
		// add up all credit card payments
		$sum = (int)Set::apply('/CreditCardPayment/payment_amount', $this->_originalRecord, 'array_sum');
		// subtract from amount paid
		$amt = $this->_editingRecord['amountPaid'] - $sum;
		if ($amt == 0) {
			$this->_editingRecord = false;
			return false;
		}
		return $amt;
	}
	
/**
 * Sanitizes comments
 * 
 * @param string $old
 * @return string
 */
	function _preparePaymentComments($old) {
		return nl2br($old);
	}

	function mapData() {
		$this->_editingRecord = array(
			'Payment' => array(
				'user_id' => $this->_editingRecord['person_id'],
				'roster_id' => $this->_editingRecord['event_roster_id'],
				'amount' => $this->_editingRecord['amountPaid'],
				'payment_type_id' => 4, // consider it a cash transaction
				'number' => null,
				'transaction_id' => null,
				'payment_placed_by' => null,
				'refunded' => 0,
				'comment' => $this->_editingRecord['paymentComments'],
			)
		);
	}

}