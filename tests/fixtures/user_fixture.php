<?php
/* User Fixture generated on: 2010-06-28 08:06:54 : 1277737794 */
class UserFixture extends CakeTestFixture {
	public $name = 'User';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'username' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 32, 'key' => 'index'),
		'password' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'last_logged_in' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'flagged' => array('type' => 'boolean', 'null' => false, 'default' => NULL),
		'group_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'reset_password' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'login' => array('column' => array('username', 'password', 'active'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	// passwords are 'password'

	public $records = array(
		array(
			'id' => 1,
			'username' => 'jharris',
			'password' => '005b8f6046bb2039063d9dde0678f9f28ae38827',
			'active' => 1,
			'created' => NULL,
			'modified' => '2010-06-25 13:05:34',
			'last_logged_in' => '2010-06-25 13:05:34',
			'flagged' => 1,
			'group_id' => 1
		),
		array(
			'id' => 2,
			'username' => 'rickyrockharbor',
			'password' => '005b8f6046bb2039063d9dde0678f9f28ae38827',
			'active' => 1,
			'created' => '2010-04-07 13:55:09',
			'modified' => '2010-04-07 13:55:09',
			'last_logged_in' => NULL,
			'flagged' => 0,
			'group_id' => 8
		),
		array(
			'id' => 3,
			'username' => 'rickyrockharborjr',
			'password' => '005b8f6046bb2039063d9dde0678f9f28ae38827',
			'active' => 1,
			'created' => '2010-04-07 13:55:09',
			'modified' => '2010-04-07 13:55:09',
			'last_logged_in' => NULL,
			'flagged' => 0,
			'group_id' => 8
		),
		array(
			'id' => 4,
			'username' => 'joe',
			'password' => '005b8f6046bb2039063d9dde0678f9f28ae38827',
			'active' => 0,
			'created' => '2010-04-07 13:55:09',
			'modified' => '2010-04-07 13:55:09',
			'last_logged_in' => NULL,
			'flagged' => 0,
			'group_id' => 8
		),
		array(
			'id' => 5,
			'username' => 'bob',
			'password' => '005b8f6046bb2039063d9dde0678f9f28ae38827',
			'active' => 1,
			'created' => '2010-04-07 13:55:09',
			'modified' => '2010-04-07 13:55:09',
			'last_logged_in' => NULL,
			'flagged' => 0,
			'group_id' => 8
		),
		array(
			'id' => 6,
			'username' => 'lfrancis',
			'password' => '005b8f6046bb2039063d9dde0678f9f28ae38827',
			'active' => 1,
			'created' => '2010-04-07 13:55:09',
			'modified' => '2010-04-07 13:55:09',
			'last_logged_in' => NULL,
			'flagged' => 0,
			'group_id' => 8
		)
	);
}
