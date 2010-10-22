<?php

class HouseholdTask extends MigratorTask {

	var $_oldPkMapping = array(
		'primary_person_id' => array('person' => 'User'),
	);

	var $_oldTable = 'household';
	var $_oldPk = 'household_id';
	var $_newModel = 'Household';

	function mapData() {
		$this->_editingRecord = array(
			'Household' => array(
				'contact_id' => $this->_editingRecord['primary_person_id'],
			)
		);
	}

}