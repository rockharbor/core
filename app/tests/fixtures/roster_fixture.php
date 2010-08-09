<?php
/* Roster Fixture generated on: 2010-06-28 09:06:01 : 1277741461 */
class RosterFixture extends CakeTestFixture {
	var $name = 'Roster';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'involvement_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'role_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'payment_option_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'roster_status' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'user_id' => 3,
			'involvement_id' => 1,
			'role_id' => NULL,
			'payment_option_id' => 2,
			'created' => '2010-05-04 07:33:03',
			'modified' => '2010-05-04 07:33:03',
			'parent_id' => 2,
			'roster_status' => 1
		),
		array(
			'id' => 2,
			'user_id' => 2,
			'involvement_id' => 1,
			'role_id' => NULL,
			'payment_option_id' => 2,
			'created' => '2010-04-19 09:55:07',
			'modified' => '2010-04-19 11:09:11',
			'parent_id' => NULL,
			'roster_status' => 1
		),
		array(
			'id' => 3,
			'user_id' => 1,
			'involvement_id' => 2,
			'role_id' => NULL,
			'payment_option_id' => 3,
			'created' => '2010-04-19 09:32:49',
			'modified' => '2010-04-19 12:30:21',
			'parent_id' => NULL,
			'roster_status' => 1
		),
		array(
			'id' => 4,
			'user_id' => 2,
			'involvement_id' => 3,
			'role_id' => NULL,
			'payment_option_id' => 3,
			'created' => '2010-04-19 09:32:49',
			'modified' => '2010-04-19 12:30:21',
			'parent_id' => NULL,
			'roster_status' => 1
		),
		array(
			'id' => 5,
			'user_id' => 1,
			'involvement_id' => 3,
			'role_id' => NULL,
			'payment_option_id' => 3,
			'created' => '2010-04-19 09:32:49',
			'modified' => '2010-04-19 12:30:21',
			'parent_id' => NULL,
			'roster_status' => 1
		),
		array(
			'id' => 6,
			'user_id' => 5,
			'involvement_id' => 5,
			'role_id' => NULL,
			'payment_option_id' => 4,
			'created' => '2010-04-19 09:32:49',
			'modified' => '2010-04-19 12:30:21',
			'parent_id' => NULL,
			'roster_status' => 1
		)
	);
}
?>