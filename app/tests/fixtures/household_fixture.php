<?php
/* Household Fixture generated on: 2010-06-28 09:06:11 : 1277741291 */
class HouseholdFixture extends CakeTestFixture {
	var $name = 'Household';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'contact_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 22,
			'contact_id' => 54,
			'created' => '2010-05-06 08:56:36',
			'modified' => '2010-05-06 08:56:36'
		),
		array(
			'id' => 21,
			'contact_id' => 53,
			'created' => '2010-04-22 13:06:53',
			'modified' => '2010-04-22 13:06:53'
		),
		array(
			'id' => 20,
			'contact_id' => 46,
			'created' => '2010-04-08 07:35:48',
			'modified' => '2010-04-08 07:35:48'
		),
		array(
			'id' => 9,
			'contact_id' => 1,
			'created' => '2010-04-06 10:49:59',
			'modified' => '2010-04-06 10:49:59'
		),
		array(
			'id' => 19,
			'contact_id' => 44,
			'created' => '2010-04-07 13:55:09',
			'modified' => '2010-04-07 13:55:09'
		),
		array(
			'id' => 18,
			'contact_id' => 42,
			'created' => '2010-04-07 13:48:49',
			'modified' => '2010-04-07 13:48:49'
		),
		array(
			'id' => 23,
			'contact_id' => 64,
			'created' => '2010-06-02 08:59:40',
			'modified' => '2010-06-02 08:59:40'
		),
		array(
			'id' => 24,
			'contact_id' => 65,
			'created' => '2010-06-02 10:22:36',
			'modified' => '2010-06-02 10:22:36'
		),
		array(
			'id' => 25,
			'contact_id' => 66,
			'created' => '2010-06-02 10:24:21',
			'modified' => '2010-06-02 10:24:21'
		),
		array(
			'id' => 26,
			'contact_id' => 67,
			'created' => '2010-06-02 10:26:24',
			'modified' => '2010-06-02 10:26:24'
		),
	);
}
?>