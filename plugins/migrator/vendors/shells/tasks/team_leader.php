<?php

class TeamLeaderTask extends MigratorTask {

	var $_oldPkMapping =array(
		'team_id' => array('teams' => 'Involvement'),
		'person_id' => array('person' => 'User'),
	);

	var $_oldTable = 'team_roster';
	var $_oldPk = 'team_id';
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
				'model_id' => $this->_editingRecord['team_id'],
			)
		);
	}
}