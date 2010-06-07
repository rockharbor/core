<?php
class Question extends AppModel {
	var $name = 'Question';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Involvement' => array(
			'className' => 'Involvement',
			'foreignKey' => 'involvement_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	var $validate = array(
		'description' => array(	
			'rule' => 'notEmpty',
			'required' => true
		)
	);
	
	var $actsAs = array(
		'Ordered' => array(	
			'field' => 'order',
			'foreign_key' => 'involvement_id'
		)
	);

}
?>