<?php

class TeamRosterTask extends MigratorTask {

	var $_oldPkMapping = array(
		'team_id' => array('teams' => 'Involvement'),
		'person_id' => array('person' => 'User'),
		'question1_id' => array('questions' => 'Question'),
		'question2_id' => array('questions' => 'Question'),
		'question3_id' => array('questions' => 'Question'),
		'question4_id' => array('questions' => 'Question'),
		'question5_id' => array('questions' => 'Question'),
	);

	var $_oldTable = 'team_roster';
	var $_oldPk = 'team_roster_id';
	var $_newModel = 'Roster';

	function findData($limit = null) {
		$options = array(
			'order' => $this->_oldPk.' ASC',
			'conditions' => array(
				'not' => array(
					$this->_oldPk => $this->_getPreMigrated(),
					'isLeader' => 'T'
				)
			)
		);
		if ($limit) {
			$options['limit'] = $limit;
		}
		return $this->old->find('all', $options);
	}

	function mapData() {
		$answers = array();
		
		for ($i=1; $i<6; $i++) {
			if ($this->_editingRecord['question'.$i.'_id'] > 0 && !empty($this->_editingRecord['answer'.$i])) {
				$answers[] = array(
					'question_id' => $this->_editingRecord['question'.$i.'_id'],
					'description' => $this->_editingRecord['answer'.$i],
				);
			}
		}

		if (!empty($answers)) {
			$this->_editingRecord = array(
				'Roster' => array(
					'user_id' => $this->_editingRecord['person_id'],
					'involvement_id' => $this->_editingRecord['team_id'],
					'payment_option_id' => null,
					'parent_id' => null,
					'roster_status_id' => $this->_editingRecord['confirmed'] ? 1 : 2,
					'created' => $this->_editingRecord['date_joined_team']
				),
				'Answer' => $answers
			);
		} else {
			$this->_editingRecord = array(
				'Roster' => array(
					'user_id' => $this->_editingRecord['person_id'],
					'involvement_id' => $this->_editingRecord['team_id'],
					'payment_option_id' => null,
					'parent_id' => null,
					'roster_status_id' => $this->_editingRecord['confirmed'] ? 1 : 2,
					'created' => $this->_editingRecord['date_joined_team']
				),
			);
		}

	}

}