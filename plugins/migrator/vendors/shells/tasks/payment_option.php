<?php

class PaymentOptionTask extends MigratorTask {

	var $_oldPkMapping = array(
		'event_id' => array('events' => 'Involvement'),
	);

	var $_oldTable = 'payment_options';
	var $_oldPk = 'id';
	var $_newModel = 'PaymentOption';

	function mapData() {
		$this->_editingRecord = array(
			'PaymentOption' => array(
				'involvement_id' => $this->_editingRecord['event_id'],
				'name' => $this->_editingRecord['name'],
				'total' => $this->_editingRecord['total'],
				'deposit' => $this->_editingRecord['deposit'],
				'childcare' => NULL,
				'account_code' => $this->_editingRecord['account_code'],
				'tax_deductible' => 0,
				'created' => $this->_editingRecord['created'],
			)
		);
	}

}
