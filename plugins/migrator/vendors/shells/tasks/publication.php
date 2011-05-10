<?php

class PublicationTask extends MigratorTask {

	var $_oldTable = 'publication';
	var $_oldPk = 'publication_id';
	var $_newModel = 'Publication';

	function mapData() {
		$this->_editingRecord = array(
			'Publication' => array(
				'name' => $this->_editingRecord['publication_name'],
				'link' => $this->_editingRecord['publication_link'],
				'description' => $this->_editingRecord['description'],
				'created' => $this->_editingRecord['created'],
			)
		);
	}

}