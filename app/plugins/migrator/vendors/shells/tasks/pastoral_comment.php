<?php

class PastoralCommentTask extends MigratorTask {

	var $_oldPkMapping = array(
		'person_id' => array('person' => 'User'),
		'entered_by_person_id' => array('person' => 'User'),
	);

	var $_oldTable = 'pastoral_comment';
	var $_oldPk = 'pastoral_comment_id';
	var $_newModel = 'Comment';

	function mapData() {
		$this->_editingRecord = array(
			'Comment' => array(
				'user_id' => $this->_editingRecord['person_id'],
				'group_id' => 3, //Pastor
				'comment' => $this->_editingRecord['comment'],
				'created_by' => $this->_editingRecord['entered_by_person_id'],
				'created' => $this->_editingRecord['created'],
			)
		);
	}

}