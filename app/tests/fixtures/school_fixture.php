<?php
/* School Fixture generated on: 2010-06-28 09:06:11 : 1277741471 */
class SchoolFixture extends CakeTestFixture {
	var $name = 'School';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'El Dorado',
			'type' => 'h',
			'created' => '2010-05-20 07:58:11',
			'modified' => '2010-05-20 07:58:11'
		),
		array(
			'id' => 2,
			'name' => 'East Bluff',
			'type' => 'e',
			'created' => '2010-05-20 08:02:24',
			'modified' => '2010-05-20 08:02:24'
		),
		array(
			'id' => 3,
			'name' => 'Adams',
			'type' => 'm',
			'created' => '2010-05-20 08:02:36',
			'modified' => '2010-05-20 08:02:36'
		),
		array(
			'id' => 4,
			'name' => 'Azusa Pacific',
			'type' => 'c',
			'created' => '2010-05-20 08:02:56',
			'modified' => '2010-05-20 08:02:56'
		),
	);
}
?>