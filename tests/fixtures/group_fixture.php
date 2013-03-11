<?php
/* Group Fixture generated on: 2010-07-07 11:07:47 : 1278526667 */
class GroupFixture extends CakeTestFixture {
	public $name = 'Group';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 45, 'key' => 'unique'),
		'conditional' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 1),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'lft' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'rght' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'tree' => array('column' => array('lft', 'rght')), 'name_key' => array('column' => 'name', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
		array(
			'id' => 1,
			'name' => 'Super Administrator',
			'conditional' => 0,
			'created' => '2010-07-07 11:15:21',
			'modified' => '2010-07-07 11:15:21',
			'parent_id' => NULL,
			'lft' => 1,
			'rght' => 26
		),
		array(
			'id' => 2,
			'name' => 'Administrator',
			'conditional' => 0,
			'created' => '2010-07-07 11:15:21',
			'modified' => '2010-07-07 11:15:21',
			'parent_id' => 1,
			'lft' => 2,
			'rght' => 25
		),
		array(
			'id' => 3,
			'name' => 'Pastor',
			'conditional' => 0,
			'created' => '2010-07-07 11:15:21',
			'modified' => '2010-07-07 11:15:21',
			'parent_id' => 2,
			'lft' => 3,
			'rght' => 24
		),
		array(
			'id' => 4,
			'name' => 'Communications Admin',
			'conditional' => 0,
			'created' => '2010-07-07 11:15:21',
			'modified' => '2010-07-07 11:15:21',
			'parent_id' => 3,
			'lft' => 4,
			'rght' => 23
		),
		array(
			'id' => 5,
			'name' => 'Staff',
			'conditional' => 0,
			'created' => '2010-07-07 11:15:21',
			'modified' => '2010-07-07 11:15:21',
			'parent_id' => 4,
			'lft' => 5,
			'rght' => 22
		),
		array(
			'id' => 6,
			'name' => 'Intern',
			'conditional' => 0,
			'created' => '2010-07-07 11:15:21',
			'modified' => '2010-07-07 11:15:21',
			'parent_id' => 5,
			'lft' => 6,
			'rght' => 21
		),
		array(
			'id' => 7,
			'name' => 'Developer',
			'conditional' => 0,
			'created' => '2010-07-07 11:15:22',
			'modified' => '2010-07-07 11:15:22',
			'parent_id' => 6,
			'lft' => 7,
			'rght' => 20
		),
		array(
			'id' => 8,
			'name' => 'User',
			'conditional' => 0,
			'created' => '2010-07-07 11:15:22',
			'modified' => '2010-07-07 11:15:22',
			'parent_id' => 7,
			'lft' => 8,
			'rght' => 19
		),
		array(
			'id' => 9,
			'name' => 'Campus Manager',
			'conditional' => 1,
			'created' => '2010-07-07 11:15:22',
			'modified' => '2010-07-07 11:15:22',
			'parent_id' => 8,
			'lft' => 9,
			'rght' => 14
		),
		array(
			'id' => 10,
			'name' => 'Ministry Manager',
			'conditional' => 1,
			'created' => '2010-07-07 11:15:22',
			'modified' => '2010-07-07 11:15:22',
			'parent_id' => 9,
			'lft' => 10,
			'rght' => 13
		),
		array(
			'id' => 11,
			'name' => 'Involvement Leader',
			'conditional' => 1,
			'created' => '2010-07-07 11:15:22',
			'modified' => '2010-07-07 11:15:22',
			'parent_id' => 10,
			'lft' => 11,
			'rght' => 12
		),
		array(
			'id' => 12,
			'name' => 'Owner',
			'conditional' => 1,
			'created' => '2010-07-07 11:15:22',
			'modified' => '2010-07-07 11:15:22',
			'parent_id' => 8,
			'lft' => 15,
			'rght' => 18
		),
		array(
			'id' => 13,
			'name' => 'Household Contact',
			'conditional' => 1,
			'created' => '2010-07-07 11:15:22',
			'modified' => '2010-07-07 11:15:22',
			'parent_id' => 12,
			'lft' => 16,
			'rght' => 17
		)
	);
}
