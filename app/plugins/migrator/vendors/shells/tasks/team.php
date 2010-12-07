<?php

class TeamTask extends MigratorTask {

	var $_oldPkMapping = array(
		'ministry_id' => array('ministry' => 'Ministry'),
		'subministry_id' => array('ministry' => 'Ministry'),
	);

	var $_oldTable = 'teams';
	var $_oldPk = 'team_id';
	var $_newModel = 'Involvement';

	var $meetingDays = array(
		'SUN' => 0,
		'MON' => 1,
		'TUE' => 2,
		'WED' => 3,
		'THU' => 4,
		'FRI' => 5,
		'SAT' => 6,
	);

	function mapData() {
		if ($this->_editingRecord['subministry_id'] != 0) {
			$this->_editingRecord['ministry_id'] = $this->_editingRecord['subministry_id'];
		}

		$dates = array();
		$this->_editingRecord = array(
			'Involvement' => array(
				'ministry_id' => $this->_editingRecord['ministry_id'],
				'involvement_type_id' => 2, //team
				'name' => $this->_editingRecord['team_name'],
				'description' => $this->_editingRecord['purpose'],
				'roster_limit' => $this->_editingRecord['maxNumberPeople'],
				'roster_visible' => $this->_editingRecord['allowMembersViewOtherMembers'],
				'private' => !$this->_editingRecord['isPublic'],
				'signup' => true,
				'take_payment' => false,
				'offer_childcare' => false,
				'active' => $this->_editingRecord['active'],
				'force_payment' => false,
			)
		);
		if (!empty($this->_editingRecord['meetingDay'])) {
			foreach (explode(',', $this->_editingRecord['meetingDay']) as $meetingDay) {
				$dates[] = array(
					'start_date' => date('Y-m-d'),
					'end_date' => date('Y-m-d'),
					'start_time' => $this->_editingRecord['start_time'],
					'end_time' => $this->_editingRecord['end_time'],
					'all_day' => 0,
					'permanent' => 1,
					'recurring' => 1,
					'recurrance_type' => 'w',
					'frequency' => 1,
					'weekday' => $this->meetingDays[$meetingDay],
					'day' => 1,
					'exemption' => 0,
					'offset' => 0
				);
			}
			$this->_editingRecord['Date'] = $dates;
		}
	}

	function _preparePurpose($old) {
		$old = Sanitize::html($old, array(
			'remove' => true,
		));
		return Sanitize::html(nl2br($old));
	}

	function _prepareTeamName($old) {
		return Sanitize::html($old);
	}

}