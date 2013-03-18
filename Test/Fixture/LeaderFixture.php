<?php
/* Leader Fixture generated on: 2010-06-28 09:06:51 : 1277741331 */
class LeaderFixture extends CakeTestFixture {
	public $name = 'Leader';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'model' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 32),
		'model_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'is_leader_key' => array('column' => array('user_id', 'model', 'model_id'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
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
		),
		array(
			'id' => 5,
			'user_id' => 1,
			'model' => 'Involvement',
			'model_id' => 3,
			'created' => '2010-03-30 14:09:19',
			'modified' => '2010-03-30 14:09:19'
		)
	);
}
