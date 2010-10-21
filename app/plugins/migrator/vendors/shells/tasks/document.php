<?php

class ImageTask extends MigratorTask {

	var $_documentTypeMap = array(
		'EVENT' => 'Involvement',
		'TEAM' => 'Involvement',
		'GROUP' => 'Involvement',
		'PERSON' => 'User'
	);

	var $_oldTable = 'documents';
	var $_oldPk = 'document_id';
	var $_newModel = 'Attachment';

/**
 * Migrates data using the subtask's definitions
 *
 * @param integer $limit
 */
	function migrate($limit = null) {
		$this->{$this->_newModel} =& ClassRegistry::init($this->_newModel);
		if ($this->{$this->_newModel}->Behaviors->attached('Logable')) {
			$this->{$this->_newModel}->Behaviors->detach('Logable');
		}
		$this->{$this->_newModel}->model = 'Document';
		$this->old = new Model(false, $this->_oldTable, $this->_oldDbConfig);
		/**
		 * Person
		 */
		$this->_oldPkMapping =array(
			'type_id' => array('person' => 'User')
		);
		$this->migrateData('PERSON');

		/**
		 * Event
		 */
		$this->_oldPkMapping =array(
			'type_id' => array('events' => 'Involvement')
		);
		$this->migrateData('EVENT');

		/**
		 * Team
		 */
		$this->_oldPkMapping =array(
			'type_id' => array('teams' => 'Involvement')
		);
		$this->migrateData('TEAM');

		/**
		 * Group
		 */
		$this->_oldPkMapping =array(
			'type_id' => array('groups' => 'Involvement')
		);
		$this->migrateData('GROUP');

		if (!empty($this->orphans)) {
			$this->out("The following $this->_oldTable records are considered orphaned:");
			$this->out(implode(',', $this->orphans));
		}
	}

	function migrateData($type) {
		$alreadyMigrated = $this->IdLinkage->find('all', array(
			'conditions' => array(
				'old_table' => $this->_oldTable,
				'new_model' => $this->_newModel
			)
		));
		$alreadyMigrated = Set::extract('/IdLinkage/old_pk', $alreadyMigrated);

		$options = array(
			'order' => 'document_id',
			'conditions' => array(
				'not' => array(
					$this->_oldPk => $alreadyMigrated
				),
				'type_id' => $type
			)
		);
		$data = $this->old->find('all', $options);

		foreach ($data as $oldRecord) {
			$oldRecord = $oldRecord['Model'];
			$oldPk = $oldRecord[$this->_oldPk];
			$this->_editingRecord = $oldRecord;
			$this->_prepareData($this->_oldTable);
			$this->mapData();

			$this->{$this->_newModel}->create();
			$success = $this->{$this->_newModel}->saveAll($this->_editingRecord, array('validate' => false));
			if (!$success) {
				$this->out('Couldn\'t save '.$this->_newModel.' # '.$oldRecord[$this->_oldPk]);
				$this->out(print_r($this->_editingRecord));
				if ($this->in('Continue with migration?', array('y', 'n')) == 'n') {
					$this->_stop();
					break;
				}
			}

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
			$this->out('Migrated '.$this->_oldTable.' # '.$oldPk.' to '.$this->_newModel.' # '.$this->{$this->_newModel}->id.' ('.$timetook.' s)');
		}
	}

	function mapData() {
		$friendly = explode('.', $this->_editingRecord['displayname']);
		array_pop($friendly);
		$friendly = implode('.', $friendly);

		$this->_editingRecord = array(
			'Document' => array(
				'model' => $this->_editingRecord['document_type'],
				'foreign_key' => $this->_editingRecord['type_id'],
				'alternative' => low($friendly),
				'group' => 'Document',
				'approved' => true,
				'created' => $this->_editingRecord['created'],
				'file' => ROOT.DS.'attachments'.DS.$this->_editingRecord['filename']
			)
		);
	}

}