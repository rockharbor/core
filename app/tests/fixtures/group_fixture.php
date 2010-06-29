<?php
/* Group Fixture generated on: 2010-06-28 08:06:59 : 1277737859 */
class GroupFixture extends CakeTestFixture {
	var $name = 'Group';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 45),
		'conditional' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 1),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'lft' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'rght' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'Super Administrator',
			'conditional' => 0,
			'created' => '2010-06-03 09:37:08',
			'modified' => '2010-06-03 09:37:08',
			'parent_id' => NULL,
			'lft' => 1,
			'rght' => 28
		),
		array(
			'id' => 2,
			'name' => 'Administrator',
			'conditional' => 0,
			'created' => '2010-06-03 09:37:09',
			'modified' => '2010-06-03 09:37:09',
			'parent_id' => 1,
			'lft' => 2,
			'rght' => 27
		),
		array(
			'id' => 3,
			'name' => 'Pastor',
			'conditional' => 0,
			'created' => '2010-06-03 09:37:09',
			'modified' => '2010-06-03 09:37:09',
			'parent_id' => 2,
			'lft' => 3,
			'rght' => 26
		),
		array(
			'id' => 4,
			'name' => 'Communications Admin',
			'conditional' => 0,
			'created' => '2010-06-03 09:37:09',
			'modified' => '2010-06-03 09:37:09',
			'parent_id' => 3,
			'lft' => 4,
			'rght' => 25
		),
		array(
			'id' => 5,
			'name' => 'Staff',
			'conditional' => 0,
			'created' => '2010-06-03 09:37:09',
			'modified' => '2010-06-03 09:37:09',
			'parent_id' => 4,
			'lft' => 5,
			'rght' => 24
		),
		array(
			'id' => 6,
			'name' => 'Intern',
			'conditional' => 0,
			'created' => '2010-06-03 09:37:09',
			'modified' => '2010-06-03 09:37:09',
			'parent_id' => 5,
			'lft' => 6,
			'rght' => 23
		),
		array(
			'id' => 7,
			'name' => 'Volunteer',
			'conditional' => 0,
			'created' => '2010-06-03 09:37:09',
			'modified' => '2010-06-03 09:37:09',
			'parent_id' => 6,
			'lft' => 7,
			'rght' => 22
		),
		array(
			'id' => 8,
			'name' => 'Developer',
			'conditional' => 0,
			'created' => '2010-06-03 09:37:09',
			'modified' => '2010-06-03 09:37:09',
			'parent_id' => 7,
			'lft' => 8,
			'rght' => 21
		),
		array(
			'id' => 9,
			'name' => 'User',
			'conditional' => 0,
			'created' => '2010-06-03 09:37:09',
			'modified' => '2010-06-03 09:37:09',
			'parent_id' => 8,
			'lft' => 9,
			'rght' => 20
		),
		array(
			'id' => 10,
			'name' => 'Campus Manager',
			'conditional' => 1,
			'created' => '2010-06-03 09:37:09',
			'modified' => '2010-06-03 09:37:09',
			'parent_id' => 9,
			'lft' => 10,
			'rght' => 15
		),
	);
}
?>