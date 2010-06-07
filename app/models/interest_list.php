<?php
class InterestList extends AppModel {
	var $name = 'InterestList';

	
	var $belongsTo = array(
		'Ministry' => array(
			'className' => 'Ministry',
			'foreignKey' => 'ministry_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'InterestListType'
	);


}
?>