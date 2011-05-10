<?php

class ManagerTask extends MigratorTask {

	var $_oldPkMapping = array(
		'person_id' => array('person' => 'User'),
		'ministry_id' => array('ministry' => 'Ministry'),
	);

	var $_oldTable = 'ministry_manager';
	var $_oldPk = 'ministry_manager_id';
	var $_newModel = 'Leader';

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