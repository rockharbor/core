<?php
/* Leader Fixture generated on: 2010-06-28 09:06:51 : 1277741331 */
class LeaderFixture extends CakeTestFixture {
	var $name = 'Leader';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'model' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'model_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'user_id' => 1,
			'model' => 'Ministry',
			'model_id' => 4,
			'created' => '2010-03-30 14:09:19',
			'modified' => '2010-03-30 14:09:19'
		),
		array(
			'id' => 2,
			'user_id' => 1,
			'model' => 'Involvement',
			'model_id' => 1,
			'created' => '2010-04-09 07:28:57',
			'modified' => '2010-04-09 07:28:57'
		),
		array(
			'id' => 3,
			'user_id' => 1,
			'model' => 'Campus',
			'model_id' => 1,
			'created' => '2010-06-04 10:14:00',
			'modified' => '2010-06-04 10:14:00'
		),
		array(
			'id' => 4,
			'user_id' => 2,
			'model' => 'Ministry',
			'model_id' => 4,
			'created' => '2010-03-30 14:09:19',
			'modified' => '2010-03-30 14:09:19'
		)
	);
}
?>