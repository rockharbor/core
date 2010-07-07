<?php
/* User Fixture generated on: 2010-06-28 08:06:54 : 1277737794 */
class UserFixture extends CakeTestFixture {
	var $name = 'User';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'username' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'password' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'last_logged_in' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'flagged' => array('type' => 'boolean', 'null' => false, 'default' => NULL),
		'group_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'username' => 'jharris',
			'password' => 'G���Y3�',
			'active' => 1,
			'created' => NULL,
			'modified' => '2010-06-25 13:05:34',
			'last_logged_in' => '2010-06-25 13:05:34',
			'flagged' => 1,
			'group_id' => 3
		),
		array(
			'id' => 2,
			'username' => 'rickyrockharbor',
			'password' => '��%*�F�',
			'active' => 1,
			'created' => '2010-04-07 13:55:09',
			'modified' => '2010-04-07 13:55:09',
			'last_logged_in' => NULL,
			'flagged' => 0,
			'group_id' => 9
		),
		array(
			'id' => 3,
			'username' => 'rickyrockharborjr',
			'password' => '��%*�F�',
			'active' => 1,
			'created' => '2010-04-07 13:55:09',
			'modified' => '2010-04-07 13:55:09',
			'last_logged_in' => NULL,
			'flagged' => 0,
			'group_id' => 9
		)
	);
}
?>