<?php
class InvitationsUserFixture extends CakeTestFixture {
	var $name = 'InvitationsUser';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'invitation_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'join' => array('column' => array('invitation_id', 'user_id'), 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'invitation_id' => 2,
			'user_id' => 1
		),
		array(
			'id' => 2,
			'invitation_id' => 2,
			'user_id' => 3
		)
	);
}
?>