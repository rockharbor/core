<?php

class ZipcodeTask extends MigratorTask {

	var $_oldPkMapping = array(
		'region_id' => array('region' => 'Region'),
	);

	var $_oldTable = 'region_zip_code';
	var $_oldPk = 'region_zip_code_id';
	var $_newModel = 'Zipcode';

	function mapData() {
		$this->_editingRecord = array(
			'Zipcode' => array(
				'region_id' => $this->_editingRecord['region_id'],
				'zip' => $this->_editingRecord['zip'],
			)
		);
	}

}