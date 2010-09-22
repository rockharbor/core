<?php
class Queue extends QueueEmailAppModel {
	
	var $name = 'Queue';
	
	var $validate = array(
		'to' => array(
			'rule' => 'notempty',
			'required' => true
		),
		'from' => array(
			'rule' => 'notempty',
			'required' => true
		),
	);
}
?>