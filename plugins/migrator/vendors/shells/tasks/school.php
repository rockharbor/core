<?php

class SchoolTask extends MigratorTask {

	var $_oldTable = 'schools';
	var $_oldPk = 'school_id';
	var $_newModel = 'School';

	var $_schoolTypeMap = array(
		'GRADE' => 'e',
		'MIDDLE' => 'm',
		'HIGH' => 'h',
		'COLLEGE' => 'c'
	);

	function mapData() {
		$this->_editingRecord = array(
			'School' => array(
				'name' => $this->_editingRecord['school_name'],
				'type' => $this->_editingRecord['school_type'],
			)
		);
	}

	function _prepareSchoolName($old) {
		return ucwords($old);
	}

}