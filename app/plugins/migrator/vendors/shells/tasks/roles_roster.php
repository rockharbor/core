<?php

class RolesRosterTask extends MigratorTask {

	var $_oldTable = 'role_assignments';
	var $_oldPk = 'role_assignment_id';
	var $_newModel = 'RolesRoster';

	function migrate($limit = null) {
		$this->roster = ClassRegistry::init('Roster');

		$this->_initModels();

		/**
		 * Event
		 */
		$this->_oldPkMapping =array(
			'person_id' => array('person' => 'User'),
			'role_id' => array('role' => 'Role'),
			'type_id' => array('events' => 'Involvement')
		);
		$oldData = $this->findData($limit, 'EVENT');
		$this->_migrate($oldData);

		/**
		 * Team
		 */
		$this->_oldPkMapping =array(
			'person_id' => array('person' => 'User'),
			'role_id' => array('role' => 'Role'),
			'type_id' => array('teams' => 'Involvement')
		);
		$oldData = $this->findData($limit, 'TEAM');
		$this->_migrate($oldData);

		/**
		 * Group
		 */
		$this->_oldPkMapping =array(
			'person_id' => array('person' => 'User'),
			'role_id' => array('role' => 'Role'),
			'type_id' => array('groups' => 'Involvement')
		);
		$oldData = $this->findData($limit, 'GROUP');
		$this->_migrate($oldData);

		if (!empty($this->orphans)) {
			$this->out("The following $this->_oldTable records are considered orphaned:");
			$this->out(implode(',', $this->orphans));
			if ($this->in('Continue with migration?', array('y', 'n')) == 'n') {
				$this->_stop();
				break;
			}
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
		$type = strtolower($this->_editingRecord['type']);
		$roster = $this->roster->find('first', array(
			'conditions' => array(
				'user_id' => $this->_editingRecord['person_id'],
				'involvement_id' => $this->_editingRecord['type_id'],
			)
		));

		$this->_editingRecord = array(
			'RolesRoster' => array(
				'role_id' => $this->_editingRecord['role_id'],
				'roster_id' => $roster['Roster']['id']
			)
		);
	}

}