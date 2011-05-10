<?php

class RoleTask extends MigratorTask {

	var $_oldTable = 'role';
	var $_oldPk = 'role_id';
	var $_newModel = 'Role';

	var $_oldPkMapping =array(
		'ministry_id' => array('ministry' => 'Ministry')
	);

	function findData($limit = null) {
		$options = array(
			'order' => $this->_oldPk.' ASC',
			'conditions' => array(
				'not' => array(
					$this->_oldPk => $this->_getPreMigrated(),
					'active' => 'F'
				)
			)
		);
		if ($limit) {
			$options['limit'] = $limit;
		}
		return $this->old->find('all', $options);
	}

	function mapData() {
		$this->_editingRecord = array(
			'Role' => array(
				'ministry_id' => $this->_editingRecord['ministry_id'],
				'name' => $this->_editingRecord['role_name'],
				'description' => null,
			)
		);
	}

}