<?php

class QuestionTask extends MigratorTask {

	var $_oldTable = 'questions';
	var $_oldPk = 'question_id';
	var $_newModel = 'Question';

	function migrate($limit = null) {
		$this->_initModels();
		$this->Question->Behaviors->detach('Ordered');

		/**
		 * Event
		 */
		$this->_oldPkMapping =array(
			'type_id' => array('events' => 'Involvement')
		);
		$oldData = $this->findData($limit, 'EVENT');
		$this->_migrate($oldData);

		/**
		 * Team
		 */
		$this->_oldPkMapping =array(
			'type_id' => array('teams' => 'Involvement')
		);
		$oldData = $this->findData($limit, 'TEAM');
		$this->_migrate($oldData);

		/**
		 * Group
		 */
		$this->_oldPkMapping =array(
			'type_id' => array('groups' => 'Involvement')
		);
		$oldData = $this->findData($limit, 'GROUP');
		$this->_migrate($oldData);

		if (!empty($this->orphans)) {
			CakeLog::write('migration', $this->_oldTable.' with orphan links: '.implode(',', $this->orphans));
		}
	}

	function findData($limit = null, $type = null) {
		$options = array(
			'order' => $this->_oldPk,
			'conditions' => array(
				'not' => array(
					$this->_oldPk => $this->_getPreMigrated()
				),
				'type' => $type
			)
		);
		if ($limit) {
			$options['limit'] = $limit;
		}
		return $this->old->find('all', $options);
	}

	function mapData() {
		$this->_editingRecord = array(
			'Question' => array(
				'involvement_id' => $this->_editingRecord['type_id'],
				'order' => ((int)$this->_editingRecord['question_order']+1),
				'description' => $this->_editingRecord['question_text'],
			)
		);
	}

}