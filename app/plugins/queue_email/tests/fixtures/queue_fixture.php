<?php
/* Queue Fixture generated on: 2010-09-17 11:09:12 : 1284748272 */
class QueueFixture extends CakeTestFixture {
	var $name = 'Queue';

	var $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary'),
		'to' => array('type' => 'string', 'default' => NULL),
		'cc' => array('type' => 'string', 'default' => NULL),
		'bcc' => array('type' => 'string', 'default' => NULL),
		'from' => array('type' => 'string', 'default' => NULL),
		'subject' => array('type' => 'string', 'default' => NULL),
		'delivery' => array('type' => 'string', 'default' => NULL, 'length' => 4),
		'smtp_options' => array('type' => 'string', 'default' => NULL),
		'message' => array('type' => 'text', 'default' => NULL),
		'header' => array('type' => 'text', 'default' => NULL),
		'created' => array('type' => 'datetime', 'default' => NULL),
		'modified' => array('type' => 'datetime', 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'to' => 'test@test.com',
			'cc' => null,
			'bcc' => null,
			'from' => 'test@test.com',
			'subject' => 'Mail',
			'delivery' => null,
			'smtp_options' => null,
			'message' => null,
			'header' => null,
			'created' => '2010-09-20 00:00:01',
			'modified' => '2010-09-20 00:00:01',
		),
		array(
			'id' => 2,
			'to' => 'test@test.com',
			'cc' => null,
			'bcc' => null,
			'from' => 'test@test.com',
			'subject' => 'Mail',
			'delivery' => 'smtp',
			'smtp_options' => 'a:4:{s:4:"port";i:25;s:4:"host";s:19:"example.smtp.server";s:8:"username";s:8:"username";s:8:"password";s:8:"password";}',
			'message' => null,
			'header' => null,
			'created' => '2010-09-20 00:00:01',
			'modified' => '2010-09-20 00:00:01',
		),
		array(
			'id' => 3,
			'to' => 'test@test.com',
			'cc' => null,
			'bcc' => null,
			'from' => 'test@test.com',
			'subject' => 'Mail',
			'delivery' => null,
			'smtp_options' => null,
			'message' => null,
			'header' => null,
			'created' => '2010-09-20 00:00:01',
			'modified' => '2010-09-20 00:00:01',
		),
		array(
			'id' => 4,
			'to' => 'test@test.com',
			'cc' => null,
			'bcc' => null,
			'from' => 'test@test.com',
			'subject' => 'Mail',
			'delivery' => null,
			'smtp_options' => null,
			'message' => null,
			'header' => null,
			'created' => '2010-09-20 00:00:01',
			'modified' => '2010-09-20 00:00:01',
		),
	);
}
?>