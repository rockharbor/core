<?php
/* Address Fixture generated on: 2010-06-28 09:06:17 : 1277741177 */
class AddressFixture extends CakeTestFixture {
	var $name = 'Address';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'address_line_1' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'address_line_2' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'city' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'state' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 2),
		'zip' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'lat' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '10,7'),
		'lng' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '10,7'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'foreign_key' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'model' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 32),
		'primary' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'model_key' => array('column' => array('foreign_key', 'model'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'Work',
			'address_line_1' => '3080 Airway',
			'address_line_2' => '',
			'city' => 'Costa Mesa',
			'state' => 'CA',
			'zip' => 92886,
			'lat' => 33.6732979,
			'lng' => -117.8743896,
			'created' => '2010-02-24 09:55:30',
			'modified' => '2010-04-05 10:25:58',
			'foreign_key' => 1,
			'model' => 'User',
			'primary' => 0,
			'active' => 0
		),
		array(
			'id' => 2,
			'name' => 'Home',
			'address_line_1' => '445 S. Pixley St.',
			'address_line_2' => '',
			'city' => 'Orange',
			'state' => 'CA',
			'zip' => 92868,
			'lat' => 33.7815781,
			'lng' => -117.8585281,
			'created' => '2010-02-24 10:52:16',
			'modified' => '2010-06-07 08:34:48',
			'foreign_key' => 1,
			'model' => 'User',
			'primary' => 1,
			'active' => 1
		),
		array(
			'id' => 3,
			'name' => 'Central Mini-lab 1',
			'address_line_1' => '3080 Airway',
			'address_line_2' => '',
			'city' => 'Costa Mesa',
			'state' => 'CA',
			'zip' => 92626,
			'lat' => 33.7815781,
			'lng' => -117.8585281,
			'created' => '2010-02-24 10:52:16',
			'modified' => '2010-06-07 08:34:48',
			'foreign_key' => 1,
			'model' => 'Involvement',
			'primary' => 1,
			'active' => 1
		)
	);
}
?>