<?php

class DateTask extends MigratorTask {

	var $_oldPkMapping = array(
		'event_id' => array('events' => 'Involvement')
	);

	var $_oldTable = 'event_dates';
	var $_oldPk = 'eventdate_id';
	var $_newModel = 'Date';

	function findData($limit = null) {
		$options = array(
			'group' => array(
				'event_id'
			),
			'order' => $this->_oldPk.' ASC',
			'conditions' => array(
				'not' => array(
					$this->_oldPk => $this->_getPreMigrated()
				)
			)
		);
		if ($limit) {
			$options['limit'] = $limit;
		}
		return $this->old->find('all', $options);
	}

	function mapData() {
		$startdatetime = explode(' ', $this->_editingRecord['start_date']);
		$enddatetime = explode(' ', $this->_editingRecord['end_date']);

		$this->_editingRecord = array(
			'Date' => array(
				'involvement_id' => $this->_editingRecord['event_id'],
				'start_date' => $startdatetime[0],
				'end_date' => $enddatetime[0],
				'start_time' => $startdatetime[1],
				'end_time' => $enddatetime[1],
				'all_day' => 0,
				'permanent' => 0,
				'recurring' => 0,
				'recurrance_type' => null,
				'frequency' => 0,
				'weekday' => null,
				'day' => 0,
				'exemption' => 0,
				'offset' => 0
			)
		);
	}

}