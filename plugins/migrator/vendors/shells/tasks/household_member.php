<?php

class HouseholdMemberTask extends MigratorTask {

	var $_oldPkMapping = array(
		'household_id' => array('household' => 'Household'),
		'person_id' => array('person' => 'User'),
	);

	var $_oldTable = 'household_member';
	var $_oldPk = 'household_member_id';
	var $_newModel = 'HouseholdMember';

	function mapData() {
		$this->_editingRecord = array(
			'HouseholdMember' => array(
				'household_id' => $this->_editingRecord['household_id'],
				'user_id' => $this->_editingRecord['person_id'],
				'confirmed' => $this->_editingRecord['confirmed'],
			)
		);
	}

}