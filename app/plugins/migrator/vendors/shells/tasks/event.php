<?php

class EventTask extends MigratorTask {

	var $_oldPkMapping = array(
		'ministry_id' => array('ministry' => 'Ministry')
	);

	var $_oldTable = 'events';
	var $_oldPk = 'event_id';
	var $_newModel = 'Involvement';

	function mapData() {
		if ($this->_editingRecord['subministry_id'] != 0) {
			$this->_editingRecord['ministry_id'] = $this->_editingRecord['subministry_id'];
		}

		$this->_editingRecord = array(
			'Involvement' => array(
				'ministry_id' => $this->_editingRecord['ministry_id'],
				'involvement_type_id' => 1, //event
				'name' => $this->_editingRecord['event_name'],
				'description' => $this->_editingRecord['description'],
				'roster_limit' => $this->_editingRecord['maxNumberPeople'],
				'roster_visible' => $this->_editingRecord['canSeeRoster'],
				'private' => !$this->_editingRecord['isPublic'],
				'signup' => $this->_editingRecord['signupRequired'],
				'take_payment' => $this->_editingRecord['allowPayment'],
				'offer_childcare' => $this->_editingRecord['offer_childcare'],
				'active' => $this->_editingRecord['active'],
				'force_payment' => $this->_editingRecord['requirePayment'],
			)
		);
	}

	function _prepareDescription($old) {
		$old = Sanitize::html($old, array(
			'remove' => true,
		));
		return Sanitize::html(nl2br($old));
	}

	function _prepareEventName($old) {
		return Sanitize::html($old);
	}

}