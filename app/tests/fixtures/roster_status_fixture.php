<?php
/* RosterStatus Fixture generated on: 2010-06-28 09:06:55 : 1277741455 */
class RosterStatusFixture extends CakeTestFixture {
	var $name = 'RosterStatus';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'Confirmed',
			'created' => '2010-03-17 09:02:14',
			'modified' => '2010-03-17 09:02:14'
		),
		array(
			'id' => 2,
			'name' => 'Pending',
			'created' => '2010-03-17 09:02:26',
			'modified' => '2010-03-17 09:02:26'
		),
	);
}
?>