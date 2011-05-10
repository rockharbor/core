<?php

class RegionTask extends MigratorTask {

	var $_oldTable = 'region';
	var $_oldPk = 'region_id';
	var $_newModel = 'Region';

	function mapData() {
		$this->_editingRecord = array(
			'Region' => array(
				'name' => $this->_editingRecord['region_name'],
			)
		);
	}

}