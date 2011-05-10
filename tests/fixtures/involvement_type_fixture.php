<?php
/* InvolvementType Fixture generated on: 2010-06-28 09:06:18 : 1277741298 */
class InvolvementTypeFixture extends CakeTestFixture {
	var $name = 'InvolvementType';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'description' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1000),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'Event',
			'created' => '2010-02-08 14:34:06',
			'modified' => '2010-02-08 14:34:06',
			'description' => ''
		),
		array(
			'id' => 2,
			'name' => 'Group',
			'created' => '2010-02-08 14:34:15',
			'modified' => '2010-02-08 14:34:24',
			'description' => ''
		),
		array(
			'id' => 3,
			'name' => 'Team',
			'created' => '2010-02-08 14:34:52',
			'modified' => '2010-03-04 12:50:16',
			'description' => ''
		),
		array(
			'id' => 5,
			'name' => 'Interest List',
			'created' => '2010-04-23 13:05:01',
			'modified' => '2010-04-23 13:05:01',
			'description' => 'Used for gathering a list of people who are interested in something.'
		),
	);
}
?>