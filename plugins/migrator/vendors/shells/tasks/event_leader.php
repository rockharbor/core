<?php

class EventLeaderTask extends MigratorTask {

	var $_oldPkMapping =array(
		'event_id' => array('events' => 'Involvement'),
		'person_id' => array('person' => 'User'),
	);

	var $_oldTable = 'event_roster';
	var $_oldPk = 'event_id';
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
				'model_id' => $this->_editingRecord['event_id'],
			)
		);
	}
}