<?php

class PaymentTask extends MigratorTask {

	var $_oldPkMapping = array(
		'event_id' => array('events' => 'Involvement'),
		'event_roster_id' => array('event_roster' => 'Roster'),
		'person_id' => array('person' => 'User'),
		'payment_placed_by' => array('person' => 'User')
	);

	var $_oldTable = 'credit_card_payment';
	var $_oldPk = 'credit_card_payment_id';
	var $_newModel = 'Payment';

	function mapData() {
		$this->_editingRecord = array(
			'Payment' => array(
				'user_id' => $this->_editingRecord['person_id'],
				'roster_id' => $this->_editingRecord['event_roster_id'],
				'amount' => $this->_editingRecord['payment_amount'],
				'payment_type_id' => 1, //credit card
				'number' => $this->_editingRecord['cc_number'],
				'transaction_id' => $this->_editingRecord['transaction_id'],
				'payment_placed_by' => $this->_editingRecord['payment_placed_by'],
				'refunded' => 0,
				'created' => $this->_editingRecord['created'],
				'comment' => 'Automatically added during migration.',
				'involvement_id' => $this->_editingRecord['event_id'],
			)
		);
	}

}