<?php

class EventRosterTask extends MigratorTask {

	var $_oldPkMapping = array(
		'event_id' => array('events' => 'Involvement'),
		'person_id' => array('person' => 'User'),
		'question1_id' => array('questions' => 'Question'),
		'question2_id' => array('questions' => 'Question'),
		'question3_id' => array('questions' => 'Question'),
		'question4_id' => array('questions' => 'Question'),
		'question5_id' => array('questions' => 'Question'),
		'payment_option_id' => array('payment_options' => 'PaymentOption')
	);

	var $_oldTable = 'event_roster';
	var $_oldPk = 'event_roster_id';
	var $_newModel = 'Roster';

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
					'involvement_id' => $this->_editingRecord['event_id'],
					'payment_option_id' => $this->_editingRecord['payment_option_id'],
					'parent_id' => null,
					'roster_status' => 1
				),
				'Answer' => $answers
			);
		} else {
			$this->_editingRecord = array(
				'Roster' => array(
					'user_id' => $this->_editingRecord['person_id'],
					'involvement_id' => $this->_editingRecord['event_id'],
					'payment_option_id' => $this->_editingRecord['payment_option_id'],
					'parent_id' => null,
					'roster_status' => 1
				),
			);
		}

	}

}