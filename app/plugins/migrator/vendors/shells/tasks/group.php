<?php

class GroupTask extends MigratorTask {

	var $_oldPkMapping = array(
		'ministry_id' => array('ministry' => 'Ministry')
	);

	var $_oldTable = 'groups';
	var $_oldPk = 'group_id';
	var $_newModel = 'Involvement';

	var $_meetingDayMap = array(
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

		$this->_editingRecord = array(
			'Involvement' => array(
				'ministry_id' => $this->_editingRecord['ministry_id'],
				'involvement_type_id' => 3, //group
				'name' => $this->_editingRecord['group_name'],
				'description' => $this->_editingRecord['purpose'],
				'roster_limit' => $this->_editingRecord['maxNumberPeople'],
				'roster_visible' => $this->_editingRecord['allowMembersViewOtherMembers'],
				'private' => !$this->_editingRecord['isPublic'],
				'signup' => true,
				'take_payment' => false,
				'offer_childcare' => false,
				'active' => $this->_editingRecord['active'],
				'force_payment' => false,
			),
			'Date' => array(
				'start_date' => date('Y-m-d'),
				'end_date' => date('Y-m-d'),
				'start_time' => $this->_editingRecord['start_time'],
				'end_time' => $this->_editingRecord['end_time'],
				'all_day' => 0,
				'permanent' => 1,
				'recurring' => 1,
				'recurrance_type' => 'w',
				'frequency' => 1,
				'weekday' => $this->_editingRecord['meetingDay'],
				'day' => 1,
				'exemption' => 0,
				'offset' => 0
			)
		);
	}

}