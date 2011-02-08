<?php
/* Ministry Fixture generated on: 2010-06-28 09:06:14 : 1277741354 */
class MinistryFixture extends CakeTestFixture {
	var $name = 'Ministry';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64, 'key' => 'index'),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'campus_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'private' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'tree' => array('column' => array('lft', 'rght'), 'unique' => 1), 'campus_key' => array('column' => 'campus_id', 'unique' => 0), 'fulltext' => array('column' => array('name', 'description'), 'unique' => 0, 'type' => 'fulltext')),
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
			'private' => 0,
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
			'private' => 0,
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
			'private' => 0,
			'created' => '2010-03-08 13:26:45',
			'modified' => '2010-03-08 13:26:45',
			'active' => 0
		),
		array(
			'id' => 4,
			'name' => 'Web',
			'description' => 'web stuff',
			'parent_id' => 1,
			'lft' => 6,
			'rght' => 7,
			'campus_id' => 1,
			'private' => 0,
			'created' => '2010-03-31 08:46:15',
			'modified' => '2010-03-31 08:46:15',
			'active' => 0
		),
		array(
			'id' => 5,
			'name' => 'Downtown Reach',
			'description' => 'Connecting with the Fullerton peeps',
			'parent_id' => null,
			'lft' => 8,
			'rght' => 9,
			'campus_id' => 2,
			'private' => 1,
			'created' => '2010-03-31 08:46:15',
			'modified' => '2010-03-31 08:46:15',
			'active' => 0
		)
	);

/**
 * Recovers tree after insterting data. This way we only need to properly define
 * the parent_id's for each records
 *
 * @param object $db Instance of DB
 * @return boolean Success
 */
	function insert(&$db) {
		$success = parent::insert($db);
		if ($success) {
			ClassRegistry::init('Ministry')->recover();
		}
		return $success;
	}
}
?>