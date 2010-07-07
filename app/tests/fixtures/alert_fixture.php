<?php
/* Alert Fixture generated on: 2010-06-28 09:06:29 : 1277741189 */
class AlertFixture extends CakeTestFixture {
	var $name = 'Alert';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'description' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'group_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'importance' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 12),
		'expires' => array('type' => 'date', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'A User-level alert',
			'description' => 'Alert description 1',
			'created' => '2010-04-27 14:04:02',
			'modified' => '2010-06-02 12:27:38',
			'group_id' => 9,
			'importance' => 'medium',
			'expires' => NULL
		),
		array(
			'id' => 2,
			'name' => 'Another User-level alert',
			'description' => 'Alert description 2',
			'created' => '2010-04-27 14:04:02',
			'modified' => '2010-06-02 12:27:38',
			'group_id' => 9,
			'importance' => 'medium',
			'expires' => NULL
		),
		array(
			'id' => 3,
			'name' => 'Yet Another User-level alert',
			'description' => 'Alert description 3',
			'created' => '2010-04-27 14:04:02',
			'modified' => '2010-06-02 12:27:38',
			'group_id' => 9,
			'importance' => 'medium',
			'expires' => NULL
		),
		array(
			'id' => 4,
			'name' => 'A Staff-level alert',
			'description' => 'Alert description 4',
			'created' => '2010-04-27 14:04:02',
			'modified' => '2010-06-02 12:27:38',
			'group_id' => 5,
			'importance' => 'medium',
			'expires' => NULL
		)
	);
}
?>