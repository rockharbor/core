<?php
class Answer extends AppModel {
	var $name = 'Answer';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	
	var $validate = array(
		'description' => array(
			'rule' => 'notEmpty',
			'required' => true
		)
	);
	
	var $belongsTo = array(
		'Roster' => array(
			'className' => 'Roster',
			'foreignKey' => 'roster_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Question' => array(
			'className' => 'Question',
			'foreignKey' => 'question_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
?>