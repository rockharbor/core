<?php
/* Household Fixture generated on: 2010-06-28 09:06:11 : 1277741291 */
class HouseholdFixture extends CakeTestFixture {
	var $name = 'Household';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'contact_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'contact_key' => array('column' => 'contact_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'contact_id' => 1,
			'created' => '2010-05-06 08:56:36',
			'modified' => '2010-05-06 08:56:36'
		),
		array(
			'id' => 2,
			'contact_id' => 2,
			'created' => '2010-04-08 07:35:48',
			'modified' => '2010-04-08 07:35:48'
		),
		array(
			'id' => 3,
			'contact_id' => 3,
			'created' => '2010-04-08 07:35:48',
			'modified' => '2010-04-08 07:35:48'
		)
	);
}
?>