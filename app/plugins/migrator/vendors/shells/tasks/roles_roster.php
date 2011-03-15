<?php

class RolesRosterTask extends MigratorTask {

	var $_oldTable = 'role_assignments';
	var $_oldPk = 'role_assignment_id';
	var $_newModel = 'RolesRoster';

	function migrate($limit = null) {
		$this->Roster = ClassRegistry::init('Roster');

		$this->_initModels();

		/**
		 * Team
		 */
		$this->_oldPkMapping =array(
			'person_id' => array('person' => 'User'),
			'role_id' => array('role' => 'Role'),
			'type_id' => array('teams' => 'Involvement'),
			'ministry_id' => array('ministry' => 'Ministry')
		);
		$oldData = $this->findData($limit, 'TEAM');
		$this->_migrate($oldData);

		/**
		 * Group
		 */
		$this->_oldPkMapping =array(
			'person_id' => array('person' => 'User'),
			'role_id' => array('role' => 'Role'),
			'type_id' => array('groups' => 'Involvement'),
			'ministry_id' => array('ministry' => 'Ministry')
		);
		$oldData = $this->findData($limit, 'GROUP');
		$this->_migrate($oldData);

		if (!empty($this->orphans)) {
			CakeLog::write('migration', $this->_oldTable.' with '.count($this->orphans).' orphan links: '.implode(',', $this->orphans));
		}
	}

	function findData($limit = null, $type = null) {
		$options = array(
			'order' => $this->_oldPk,
			'conditions' => array(
				'not' => array(
					$this->_oldPk => $this->_getPreMigrated()
				),
				'type' => $type
			)
		);
		if ($limit) {
			$options['limit'] = $limit;
		}
		return $this->old->find('all', $options);
	}

	function mapData() {
		// get roster id
		$roster = $this->Roster->find('first', array(
			'conditions' => array(
				'user_id' => $this->_editingRecord['person_id'],
				'involvement_id' => $this->_editingRecord['type_id'],
				'Involvement.ministry_id' =>  $this->_editingRecord['ministry_id'],
			),
			'contain' => array(
				'Involvement' => array(
					'fields' => array('id', 'ministry_id')
				)
			)
		));
		if ($roster == false || empty($roster)) {
			$msg = "Couldn't find roster for user ".$this->_editingRecord['person_id']." in involvement ".$this->_editingRecord['type_id'];
			CakeLog::write('migration', $msg);
			$this->_editingRecord = false;
			return;
		}

		$this->_editingRecord = array(
			'RolesRoster' => array(
				'role_id' => $this->_editingRecord['role_id'],
				'roster_id' => $roster['Roster']['id']
			)
		);
	}

}