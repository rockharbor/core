<?php

class ImageTask extends MigratorTask {

	var $_ownerTypeMap = array(
		'person' => 'User',
		'ministry' => 'Ministry'
	);

	var $_oldTable = 'images';
	var $_oldPk = 'id';
	var $_newModel = 'Image';

/**
 * Migrates data using the subtask's definitions
 *
 * @param integer $limit
 */
	function migrate($limit = null) {
		$this->_initModels();
		/**
		 * Person
		 */
		$this->_oldPkMapping =array(
			'owner_id' => array('person' => 'User')
		);
		$this->{$this->_newModel}->model = 'User';
		$oldData = $this->findData($limit, 'person');
		$this->_migrate($oldData);

		/**
		 * Ministry
		 */
		$this->_oldPkMapping =array(
			'owner_id' => array('ministry' => 'Ministry')
		);
		$this->{$this->_newModel}->model = 'Ministry';
		$oldData = $this->findData($limit, 'ministry');
		$this->_migrate($oldData);

		if (!empty($this->orphans)) {
			CakeLog::write('migration', $this->_oldTable.' with orphan links: '.implode(',', $this->orphans));
		}
	}

	function findData($limit = null, $type = null) {
		$options = array(
			'order' => 'id',
			'conditions' => array(
				'masterImage_id' => 0,
				'not' => array(
					$this->_oldPk => $this->_getPreMigrated()
				),
				'owner_type' => $type
			)
		);
		if ($limit) {
			$options['limit'] = $limit;
		}
		return $this->old->find('all', $options);
	}

	function mapData() {
		$this->{$this->_newModel}->Behaviors->Transfer->runtime[$this->_newModel]['isPrepared'] = false;
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
	}

}