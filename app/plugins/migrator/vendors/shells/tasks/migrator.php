<?php

App::import('Core', 'Model');

class MigratorTask extends MigratorShell {

	var $_booleanMap = array(
		'UNKNOWN' => null,
		'T' => true,
		'F' => false,
	);

	var $orphans = array();

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
			$this->out("The following $this->_oldTable records are considered orphaned:");
			$this->out(implode(',', $this->orphans));
			if ($this->in('Continue with migration?', array('y', 'n')) == 'n') {
				$this->_stop();
				break;
			}
		}
	}

/**
 * Migrates old data
 *
 * @param array $data
 */
	function _migrate($data) {
		foreach ($data as $oldRecord) {
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
			if ($this->addLinkages) {
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
			}
			//$this->out('link: '.(microtime(true)-$start));
			$timetook = (microtime(true)-$timestart);
			$this->out('Migrated '.$this->_oldTable.' # '.$oldPk.' to '.$this->_newModel.' # '.$this->{$this->_newModel}->id.' ('.$timetook.' s)');
		}
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
 *
 * @param array $oldCrappyRecord The old record
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
						$this->out("Couldn't find new PK for $oldTable # $oldCrappyData when looking for");
						$this->out("a match $oldTable ($oldCrappyData) > $newModel");
						$this->out("Something may have been migrated out of order!");
						$this->orphans[] = $this->_editingRecord[$this->_oldPk];
					} elseif ($oldCrappyData > 0) {
						$oldCrappyData = $link['IdLinkage']['new_pk'];
					}
				}
			}
			if (method_exists($this, '_prepare'.Inflector::camelize($oldCrappyField))) {
				$oldCrappyData = $this->{'_prepare'.Inflector::camelize($oldCrappyField)}($oldCrappyData);
			}
			if (isset($this->_booleanMap[$oldCrappyData])) {
				$oldCrappyData = $this->_booleanMap[$oldCrappyData];
			}
			if (isset($this->{'_'.lcfirst(Inflector::camelize($oldCrappyField).'Map')})) {
				$oldCrappyData = $this->{'_'.lcfirst(Inflector::camelize($oldCrappyField).'Map')}[$oldCrappyData];
			}
		}
	}

}