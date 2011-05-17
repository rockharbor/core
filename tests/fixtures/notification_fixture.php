<?php
/* Notification Fixture generated on: 2010-06-28 08:06:56 : 1277737736 */
class NotificationFixture extends CakeTestFixture {
	var $name = 'Notification';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'read' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'type' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'body' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 500),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'user_key' => array('column' => 'user_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'user_id' => 1,
			'created' => '2010-06-24 14:37:38',
			'modified' => '2010-06-24 14:37:38',
			'read' => 0,
			'type' => 'invitation',
			'body' => 'You have been invited somewhere.'
		),
		array(
			'id' => 2,
			'user_id' => 1,
			'created' => '2010-06-04 10:24:49',
			'modified' => '2010-06-24 10:21:54',
			'read' => 0,
			'type' => 'default',
			'body' => 'Jeremy Harris is now managing the campus Fischer.'
		),
		array(
			'id' => 3,
			'user_id' => 2,
			'created' => '2010-06-04 10:25:12',
			'modified' => '2010-06-04 10:25:12',
			'read' => 0,
			'type' => 'default',
			'body' => 'ricky rockharbor is no longer managing the ministry Communications.'
		),
		array(
			'id' => 4,
			'user_id' => 2,
			'created' => '2010-06-04 10:20:25',
			'modified' => '2010-06-04 10:20:25',
			'read' => 0,
			'type' => 'default',
			'body' => 'ricky rockharbor is now managing the ministry Communications.'
		)
	);
}
?>