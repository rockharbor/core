<?php

class ImageTask extends MigratorTask {

	var $_ownerTypeMap = array(
		'person' => 'User',
		'ministry' => 'Ministry'
	);

	var $_oldTable = 'images';
	var $_oldPk = 'id';
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
		$this->{$this->_newModel}->model = 'Image';
		$this->old = new Model(false, $this->_oldTable, $this->_oldDbConfig);
		/**
		 * Person
		 */
		$this->_oldPkMapping =array(
			'owner_id' => array('person' => 'User')
		);
		$this->migrateData('person');

		/**
		 * Ministry
		 */
		$this->_oldPkMapping =array(
			'owner_id' => array('ministry' => 'Ministry')
		);
		$this->migrateData('ministry');

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
			'order' => 'id',
			'conditions' => array(
				'not' => array(
					$this->_oldPk => $alreadyMigrated
				),
				'owner_type' => $type
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
		$this->_editingRecord = array(
			'Image' => array(
				'model' => $this->_editingRecord['owner_type'],
				'foreign_key' => $this->_editingRecord['owner_id'],
				'alternative' => 'profile photo',
				'group' => 'Image',
				'approved' => true,
				'created' => $this->_editingRecord['created'],
				'file' => ROOT.DS.'attachments'.DS.$this->_editingRecord['filename'].'.'.$this->_editingRecord['extension']
			)
		);

		$this->Image->model = $newAttachment['Image']['model'];
	}

}