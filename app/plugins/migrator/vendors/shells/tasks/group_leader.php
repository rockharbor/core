<?php

class GroupLeaderTask extends MigratorTask {

	var $_oldPkMapping =array(
		'group_id' => array('groups' => 'Involvement'),
		'person_id' => array('person' => 'User'),
	);

	var $_oldTable = 'group_roster';
	var $_oldPk = 'group_id';
	var $_newModel = 'Leader';

	var $addLinkages = false;

	function findData($limit = null) {
		$options = array(
			'order' => $this->_oldPk,
			'conditions' => array(
				'isLeader' => 'T'
			)
		);
		if ($limit) {
			$options['limit'] = $limit;
		}
		return $this->old->find('all', $options);
	}

	function mapData() {
		$this->_editingRecord = array(
			'Leader' => array(
				'user_id' => $this->_editingRecord['person_id'],
				'model' => 'Involvement',
				'model_id' => $this->_editingRecord['group_id'],
			)
		);
	}
}