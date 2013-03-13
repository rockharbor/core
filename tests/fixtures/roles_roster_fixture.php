<?php
class RolesRosterFixture extends CakeTestFixture {
	public $name = 'RolesRoster';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'role_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'roster_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'join_key' => array('column' => array('role_id', 'roster_id'), 'unique' => 1), 'role_key' => array('column' => 'role_id', 'unique' => 0), 'roster_key' => array('column' => 'roster_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
		array(
			'id' => 1,
			'role_id' => 1,
			'roster_id' => 5
		),
		array(
			'id' => 2,
			'role_id' => 2,
			'roster_id' => 5
		),
		array(
			'id' => 3,
			'role_id' => 3,
			'roster_id' => 3
		)
	);
}
