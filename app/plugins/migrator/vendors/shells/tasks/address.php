<?php

class AddressTask extends MigratorTask {

	var $_oldPkMapping = array(
		'person_id' => array('person' => 'User')
	);

	var $_oldTable = 'address';
	var $_oldPk = 'address_id';
	var $_newModel = 'Address';

	function mapData() {
		$this->Address->Behaviors->detach('GeoCoordinate');

		$this->_editingRecord = array(
			'Address' => array(
				'id' => $this->_editingRecord['address_id'],
				'name' => $this->_editingRecord['address_label'],
				'address_line_1' => $this->_editingRecord['address_line1'],
				'address_line_2' => $this->_editingRecord['address_line2'],
				'city' => $this->_editingRecord['city'],
				'state' => $this->_editingRecord['state'],
				'zip' => $this->_editingRecord['zip'],
				'lat' => $this->_editingRecord['lat'],
				'lng' => $this->_editingRecord['lng'],
				'created' => $this->_editingRecord['created'],
				'modified' => $this->_editingRecord['removed'],
				'foreign_key' => $this->_editingRecord['person_id'],
				'model' => 'User',
				'primary' => $this->_editingRecord['is_current'],
				'active' => $this->_editingRecord['is_active'],
			)
		);
	}

}