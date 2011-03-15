<?php

App::import('Core', 'Model', 'Validation');

class MigratorTask extends MigratorShell {

	var $_booleanMap = array(
		'UNKNOWN' => null,
		'T' => true,
		'F' => false,
	);

	var $orphans = array();

	var $addLinkages = true;

/**
 * Migrates data using the subtask's definitions
 *
 * @param integer $limit
 */
	function migrate($limit = null) {
		$this->_initModels();
		$oldData = $this->findData($limit);
		$this->_migrate($oldData);

		if (!empty($this->orphans)) {
			CakeLog::write('migration', $this->_oldTable.' with '.count($this->orphans).' orphan links: '.implode(',', $this->orphans));
		}
	}

/**
 * Migrates old data
 *
 * @param array $data
 */
	function _migrate($data) {
		$timestart = microtime(true);
		foreach ($data as $oldRecord) {			
			$oldRecord = $oldRecord['Model'];
			$oldPk = $oldRecord[$this->_oldPk];
			$this->_editingRecord = $oldRecord;
			$this->_originalRecord = $oldRecord;
			$this->_prepareData();
			$this->mapData();

			if ($this->_editingRecord == false) {
				continue;
			}

			$this->{$this->_newModel}->create();
			$success = $this->{$this->_newModel}->saveAll($this->_editingRecord);
			if (!$success) {
				$msg = 'Couldn\'t save '.$this->_newModel.' ('.$this->_oldTable.' # '.$oldRecord[$this->_oldPk].')';
				$this->out($msg);
				CakeLog::write('migration', $msg);
			}
			if (!$this->{$this->_newModel}->saveAll($this->_editingRecord, array('validate' => 'only'))) {
				CakeLog::write('migration', $this->_newModel.' ('.$this->_oldTable.' # '.$oldRecord[$this->_oldPk].') would have failed validation');
				if (!empty($this->{$this->_newModel}->validationErrors)) {
					CakeLog::write('migration', print_r($this->{$this->_newModel}->validationErrors, true));
				}
			}

			if ($success && $this->addLinkages) {
				// save new/old pk map				
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
		}
		$timetook = (microtime(true)-$timestart);
		$this->out('Migrated '.$this->_oldTable.' to '.$this->_newModel.' ('.$timetook.' s)');
	}

/**
 * Gets old data
 *
 * @param integer $limit
 * @return array
 */
	function findData($limit = null) {
		$options = array(
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

/**
 * mapData needs to be overridden in subtasks and should map the data to the
 * correct fields on the new model
 */
	function mapData() {
		$this->out('ERROR: mapData needs to be overridden in subtask');
		$this->_stop();
	}

/**
 * Initializes models
 */
	function _initModels() {
		$this->{$this->_newModel} =& ClassRegistry::init($this->_newModel);
		if ($this->{$this->_newModel}->Behaviors->attached('Logable')) {
			$this->{$this->_newModel}->Behaviors->detach('Logable');
		}
		$this->old = new Model(false, $this->_oldTable, $this->_oldDbConfig);
	}

/**
 * Gets a list of any pk's that have already migrated for this table/model
 *
 * @return array
 */
	function _getPreMigrated() {
		// import all
		$alreadyMigrated = $this->IdLinkage->find('all', array(
			'conditions' => array(
				'old_table' => $this->_oldTable,
				'new_model' => $this->_newModel
			)
		));
		return Set::extract('/IdLinkage/old_pk', $alreadyMigrated);
	}

/**
 * An old record
 *
 * Will run a field through _prepareFieldName method if it exists
 * Will map a field's data to _fieldNameMap if the var exists
 * Looks up old pks and replaces them with the new ones
 */
	function _prepareData() {		
		foreach ($this->_editingRecord as $oldCrappyField => &$oldCrappyData) {
			if (!is_string($oldCrappyData) && !is_numeric($oldCrappyData)) {				
				continue;
			}
			if (!empty($this->_oldPkMapping)) {
				// get just the pks for this new model
				if (isset($this->_oldPkMapping[$oldCrappyField])) {
					$oldTable = key($this->_oldPkMapping[$oldCrappyField]);
					$newModel = $this->_oldPkMapping[$oldCrappyField][$oldTable];
					$start = microtime(true);
					$link = $this->IdLinkage->find('first', array(
						'conditions' => array(
							'new_model' => $newModel,
							'old_table' => $oldTable,
							'old_pk' => $oldCrappyData
						)
					));
					if (empty($link) && $oldCrappyData > 0) {
						$this->orphans[] = $this->_editingRecord[$this->_oldPk];
						$msg = "Missing linkage for $oldTable # $oldCrappyData when adding checking $newModel";
						CakeLog::write('migration', $msg);
						$oldCrappyData = null;
					} elseif ($oldCrappyData > 0) {
						$oldCrappyData = $link['IdLinkage']['new_pk'];
					}
				}
			}
			if (method_exists($this, '_prepare'.Inflector::camelize($oldCrappyField))) {
				$oldCrappyData = $this->{'_prepare'.Inflector::camelize($oldCrappyField)}($oldCrappyData);
				if ($this->_editingRecord === false) {
					return;
				}
			}
			if (isset($this->{'_'.lcfirst(Inflector::camelize($oldCrappyField).'Map')})) {
				$oldCrappyData = $this->{'_'.lcfirst(Inflector::camelize($oldCrappyField).'Map')}[$oldCrappyData];
			}
			if (isset($this->_booleanMap[$oldCrappyData])) {
				$oldCrappyData = $this->_booleanMap[$oldCrappyData];
			}
		}
	}

}