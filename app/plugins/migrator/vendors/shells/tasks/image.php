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
		$this->_initModels();
		$this->{$this->_newModel}->model = 'Image';
		/**
		 * Person
		 */
		$this->_oldPkMapping =array(
			'owner_id' => array('person' => 'User')
		);
		$oldData = $this->findData($limit, 'person');
		$this->_migrate($oldData);

		/**
		 * Ministry
		 */
		$this->_oldPkMapping =array(
			'owner_id' => array('ministry' => 'Ministry')
		);
		$oldData = $this->findData($limit, 'ministry');
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

	function findData($limit = null, $type = null) {
		$options = array(
			'order' => 'id',
			'conditions' => array(
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