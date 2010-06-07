<?php
class Publication extends AppModel {
	var $name = 'Publication';
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please fill in the name.'
			)
		),
		'description' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please fill in the description.'
			)
		)
	);
	
	var $hasAndBelongsToMany = array(
		'User'
	);
}
?>