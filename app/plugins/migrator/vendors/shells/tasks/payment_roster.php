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
				'paymentComments !=' => ''
			)
		);
		if ($limit) {
			$options['limit'] = $limit;
		}
		return $this->old->find('all', $options);
	}

	function mapData() {
		$this->_editingRecord = array(
			'Payment' => array(
				'user_id' => $this->_editingRecord['person_id'],
				'roster_id' => $this->_editingRecord['event_roster_id'],
				'amount' => $this->_editingRecord['amountPaid'],
				'payment_type_id' => null, // unknown
				'number' => null,
				'transaction_id' => null,
				'payment_placed_by' => null,
				'refunded' => 0,
				'comment' => $this->_editingRecord['paymentComments'],
			)
		);
	}

}