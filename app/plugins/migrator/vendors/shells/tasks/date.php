<?php

class GroupTask extends MigratorTask {

	var $_oldPkMapping = array(
		'involvement_id' => array('events' => 'Involvement')
	);

	var $_oldTable = 'event_dates';
	var $_oldPk = 'eventdate_id';
	var $_newModel = 'Date';


/**
 * Migrates data using the subtask's definitions
 *
 * @param integer $limit
 */
	function migrate($limit = null) {
		// import all
		$alreadyMigrated = $this->IdLinkage->find('all', array(
			'conditions' => array(
				'old_table' => $this->_oldTable,
				'new_model' => $this->_newModel
			)
		));
		$alreadyMigrated = Set::extract('/IdLinkage/old_pk', $alreadyMigrated);

		$old = new Model(false, $this->_oldTable, $this->_oldDbConfig);
		// get all unique ids
		$options = array(
			'group' => array(
				'event_id'
			),
			'order' => 'eventdate_id',
			'conditions' => array(
				'not' => array(
					$this->_oldPk => $alreadyMigrated
				)
			)
		);
		if ($limit) {
			$options['limit'] = $limit;
		}
		$oldData = $old->find('all', $options);

		$this->{$this->_newModel} =& ClassRegistry::init($this->_newModel);
		if ($this->{$this->_newModel}->Behaviors->attached('Logable')) {
			$this->{$this->_newModel}->Behaviors->detach('Logable');
		}

		foreach ($oldData as $oldRecord) {
			$timestart = microtime(true);
			$oldRecord = $oldRecord['Model'];
			$oldPk = $oldRecord[$this->_oldPk];
			$this->_editingRecord = $oldRecord;
			$start = microtime(true);
			$this->_prepareData($this->_oldTable);
			//$this->out('prepare: '.(microtime(true)-$start));
			$start = microtime(true);
			$this->mapData();
			//$this->out('map: '.(microtime(true)-$start));

			$start = microtime(true);
			$this->{$this->_newModel}->create();
			//$this->out('create: '.(microtime(true)-$start));
			$start = microtime(true);
			$success = $this->{$this->_newModel}->saveAll($this->_editingRecord, array('validate' => false));
			//$this->out('save: '.(microtime(true)-$start));
			if (!$success) {
				$this->out('Couldn\'t save '.$this->_newModel.' # '.$oldRecord[$this->_oldPk]);
				$this->out(print_r($this->_editingRecord));
				if ($this->in('Continue with migration?', array('y', 'n')) == 'n') {
					$this->_stop();
					break;
				}
			}

			$start = microtime(true);
			// save new/old pk map
			if (!in_array($oldPk, $this->orphans)) {
				$this->IdLinkage->create();
				$this->IdLinkage->save(array(
					'IdLinkage' => array(
						'old_pk' => $oldPk,
						'old_table' => $this->_oldTable,
						'new_pk' => $this->{$this->_newModel}->id,
						'new_model' => $this->_newModel
					)
				));
			}
			//$this->out('link: '.(microtime(true)-$start));
			$timetook = (microtime(true)-$timestart);
			$this->out('Migrated '.$this->_oldTable.' # '.$oldPk.' to '.$this->_newModel.' # '.$this->{$this->_newModel}->id.' ('.$timetook.' s)');
		}

		if (!empty($this->orphans)) {
			$this->out("The following $this->_oldTable records are considered orphaned:");
			$this->out(implode(',', $this->orphans));
		}
	}

	function mapData() {
		$startdatetime = explode(' ', $this->_editingRecord['start_date']);
		$enddatetime = explode(' ', $this->_editingRecord['end_date']);

		$this->_editingRecord = array(
			'Date' => array(
				'involvement_id' => $this->_editingRecord['involvement_id'],
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