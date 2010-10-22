<?php
class RolesRosterFixture extends CakeTestFixture {
	var $name = 'RolesRoster';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'role_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'roster_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
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
?>