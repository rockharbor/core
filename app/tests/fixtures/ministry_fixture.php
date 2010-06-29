<?php
/* Ministry Fixture generated on: 2010-06-28 09:06:14 : 1277741354 */
class MinistryFixture extends CakeTestFixture {
	var $name = 'Ministry';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'campus_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'group_id' => array('type' => 'integer', 'null' => true, 'default' => '9', 'length' => 10),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'Communications',
			'description' => 'Description',
			'parent_id' => NULL,
			'lft' => 5,
			'rght' => 8,
			'campus_id' => 1,
			'group_id' => 9,
			'created' => '2010-03-29 10:25:30',
			'modified' => '2010-04-06 12:52:40',
			'active' => 1
		),
		array(
			'id' => 2,
			'name' => 'Alpha',
			'description' => 'a 7-week course to discuss the big questions of life, meaning, and the Christian faith.',
			'parent_id' => NULL,
			'lft' => 1,
			'rght' => 2,
			'campus_id' => 1,
			'group_id' => 9,
			'created' => '2010-02-09 09:44:49',
			'modified' => '2010-02-09 09:44:49',
			'active' => 1
		),
		array(
			'id' => 3,
			'name' => 'All Church',
			'description' => '',
			'parent_id' => NULL,
			'lft' => 3,
			'rght' => 4,
			'campus_id' => 1,
			'group_id' => 9,
			'created' => '2010-03-08 13:26:45',
			'modified' => '2010-03-08 13:26:45',
			'active' => 1
		),
		array(
			'id' => 4,
			'name' => 'Web',
			'description' => 'web stuff',
			'parent_id' => 1,
			'lft' => 6,
			'rght' => 7,
			'campus_id' => 1,
			'group_id' => 9,
			'created' => '2010-03-31 08:46:15',
			'modified' => '2010-03-31 08:46:15',
			'active' => 0
		),
	);
}
?>