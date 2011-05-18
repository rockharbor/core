<?php
/* Campus Fixture generated on: 2010-06-28 09:06:17 : 1277741237 */
class CampusFixture extends CakeTestFixture {
	var $name = 'Campus';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1000),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'RH Central',
			'description' => 'The original campus!',
			'active' => 1,
			'created' => '2010-02-08 14:39:06',
			'modified' => '2010-03-11 13:34:41'
		),
		array(
			'id' => 2,
			'name' => 'Fullerton',
			'description' => 'Slightly more hip than RH Central.',
			'active' => 0,
			'created' => '2010-04-14 13:45:17',
			'modified' => '2010-04-14 13:45:17'
		),
	);
}
?>