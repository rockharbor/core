<?php

class ManagerTask extends MigratorTask {

	var $_oldPkMapping = array(
		'person_id' => array('person' => 'User'),
		'ministry_id' => array('ministry' => 'Ministry'),
	);

	var $_oldTable = 'household';
	var $_oldPk = 'household_id';
	var $_newModel = 'Household';

	function mapData() {
		$this->_editingRecord = array(
			'Leader' => array(
				'user_id' => $this->_editingRecord['person_id'],
				'model' => 'Ministry',
				'model_id' => $this->_editingRecord['ministry_id'],
			)
		);
	}
}