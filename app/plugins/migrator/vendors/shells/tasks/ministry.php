<?php

class MinistryTask extends MigratorTask {

	var $_oldPkMapping = array(
		'parent_ministry_id' => array('ministry' => 'Ministry')
	);

	var $_oldTable = 'ministry';
	var $_oldPk = 'ministry_id';
	var $_newModel = 'Ministry';

	function findData($limit = null) {
		$options = array(
			'order' => 'parent_ministry_id ASC',
			'conditions' => array(
				'not' => array(
					$this->_oldPk => $this->_getPreMigrated()
				)
			)
		);
		if ($limit) {
			$options['limit'] = $limit;
		}
		return $this->old->find('all', $options);
	}

	function mapData() {
		$this->Ministry->Behaviors->detach('Confirm');
		$this->Ministry->Behaviors->detach('Tree');

		if ($this->_editingRecord['parent_ministry_id'] == 0) {
			 $this->_editingRecord['parent_ministry_id'] = null;
		}

		$this->_editingRecord = array(
			'Ministry' => array(
				'name' => $this->_editingRecord['ministry_name'],
				'description' => $this->_editingRecord['ministry_description'],
				'parent_id' => $this->_editingRecord['parent_ministry_id'],
				'campus_id' => 1,
				'private' => 0,
				'active' => $this->_editingRecord['active'],
			)
		);
	}

	function _prepareMinistryDescription($old) {
		$old = Sanitize::html($old, array(
			'remove' => true,
		));
		return Sanitize::html(nl2br($old));
	}

	function _prepareMinistryName($old) {
		return Sanitize::html($old);
	}
}