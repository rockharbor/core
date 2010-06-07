<?php
class Log extends AppModel {
	var $name = 'Log';
	var $displayField = 'title';

	var $order = 'Log.created DESC';

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
?>