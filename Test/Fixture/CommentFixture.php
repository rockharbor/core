<?php
/* Comment Fixture generated on: 2010-06-28 09:06:53 : 1277741153 */
class CommentFixture extends CakeTestFixture {
	public $name = 'Comment';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'group_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'comment' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 2500),
		'created_by' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'user_key' => array('column' => 'user_id', 'unique' => 0), 'group_key' => array('column' => 'group_id', 'unique' => 0), 'creator_key' => array('column' => 'created_by', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
		array(
			'id' => 1,
			'user_id' => 4,
			'group_id' => 2,
			'comment' => 'another comment',
			'created_by' => 1,
			'created' => '2010-03-24 09:53:55',
			'modified' => '2010-03-24 09:53:55'
		),
		array(
			'id' => 2,
			'user_id' => 1,
			'group_id' => 5,
			'comment' => 'comment\'d!',
			'created_by' => 2,
			'created' => '2010-03-24 10:04:59',
			'modified' => '2010-03-24 10:04:59'
		),
		array(
			'id' => 3,
			'user_id' => 3,
			'group_id' => 5,
			'comment' => 'test',
			'created_by' => 1,
			'created' => '2010-04-08 07:46:26',
			'modified' => '2010-04-08 07:46:26'
		),
		array(
			'id' => 4,
			'user_id' => 1,
			'group_id' => 8,
			'comment' => 'test again',
			'created_by' => 3,
			'created' => '2010-04-08 07:46:26',
			'modified' => '2010-04-08 07:46:26'
		)
	);
}
