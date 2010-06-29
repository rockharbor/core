<?php
/* CampusesRev Fixture generated on: 2010-06-28 09:06:28 : 1277741248 */
class CampusesRevFixture extends CakeTestFixture {
	var $name = 'CampusesRev';

	var $fields = array(
		'version_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'version_created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'version_id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'version_id' => 1,
			'version_created' => '2010-02-08 14:39:06',
			'id' => 1,
			'name' => 'Costa Mesa',
			'description' => 'The original campus!',
			'active' => 1
		),
		array(
			'version_id' => 2,
			'version_created' => '2010-03-11 13:34:41',
			'id' => 1,
			'name' => 'Fischer',
			'description' => 'The original campus!',
			'active' => 1
		),
		array(
			'version_id' => 3,
			'version_created' => '2010-04-14 13:45:17',
			'id' => 2,
			'name' => 'Fullerton',
			'description' => '',
			'active' => 1
		),
	);
}
?>