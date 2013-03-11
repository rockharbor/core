<?php
/* MergeRequest Fixture generated on: 2010-06-28 09:06:06 : 1277741346 */
class MergeRequestFixture extends CakeTestFixture {
	public $name = 'MergeRequest';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'model_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'merge_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'requester_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
		array(
			'id' => 1,
			'model' => 'User',
			'model_id' => 3,
			'merge_id' => 2,
			'requester_id' => 2,
			'created' => '2010-07-15 00:00:00',
			'modified' => '2010-07-15 00:00:00',
		)
	);
}
