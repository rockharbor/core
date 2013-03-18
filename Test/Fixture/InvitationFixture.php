<?php
class InvitationFixture extends CakeTestFixture {
	public $name = 'Invitation';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'body' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 500),
		'confirm_action' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 500),
		'deny_action' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 500),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
		array(
			'id' => 1,
			'user_id' => 1,
			'body' => 'You have been invited somewhere.',
			'confirm_action' => '/path/to/somewhere',
			'deny_action' => '/path/to/elsewhere',
			'created' => '2011-06-21 14:37:38',
			'modified' => '2011-06-21 14:37:38',
		),
		array(
			'id' => 2,
			'user_id' => 2,
			'body' => 'You have been invited somewhere neat and probably fancy.',
			'confirm_action' => '/approve/path',
			'deny_action' => '/path/of/disapproval',
			'created' => '2011-06-21 14:37:38',
			'modified' => '2011-06-21 14:37:38',
		),
	);
}
