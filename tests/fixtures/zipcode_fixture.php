<?php
/* Zipcode Fixture generated on: 2010-06-28 09:06:19 : 1277741479 */
class ZipcodeFixture extends CakeTestFixture {
	var $name = 'Zipcode';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'region_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'zip' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'region_id' => 1,
			'zip' => 92868,
			'created' => '2010-03-17 08:46:11',
			'modified' => '2010-03-17 08:46:11'
		),
		array(
			'id' => 5,
			'region_id' => 1,
			'zip' => 92821,
			'created' => '2010-05-04 08:52:45',
			'modified' => '2010-05-04 08:52:45'
		),
	);
}
?>