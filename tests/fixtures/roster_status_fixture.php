<?php
/* RosterStatus Fixture generated on: 2011-05-17 08:05:13 : 1305646273 */
class RosterStatusFixture extends CakeTestFixture {
	var $name = 'RosterStatus';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'Confirmed',
			'created' => '2011-05-17 08:31:09',
			'modified' => '2011-05-17 08:31:09'
		),
		array(
			'id' => 2,
			'name' => 'Pending',
			'created' => '2011-05-17 08:31:09',
			'modified' => '2011-05-17 08:31:09'
		),
		array(
			'id' => 3,
			'name' => 'Invited',
			'created' => '2011-05-17 08:31:09',
			'modified' => '2011-05-17 08:31:09'
		),
		array(
			'id' => 4,
			'name' => 'Declined',
			'created' => '2011-05-17 08:31:09',
			'modified' => '2011-05-17 08:31:09'
		)
	);
}
?>