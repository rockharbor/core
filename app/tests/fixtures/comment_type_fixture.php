<?php
/* CommentType Fixture generated on: 2010-06-28 09:06:37 : 1277741257 */
class CommentTypeFixture extends CakeTestFixture {
	var $name = 'CommentType';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'group_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'Staff',
			'group_id' => 5,
			'created' => '2010-03-24 07:59:28',
			'modified' => '2010-03-24 09:13:58'
		),
		array(
			'id' => 2,
			'name' => 'Pastoral',
			'group_id' => 3,
			'created' => '2010-03-24 08:38:20',
			'modified' => '2010-03-24 08:38:20'
		),
		array(
			'id' => 3,
			'name' => 'Admin',
			'group_id' => 2,
			'created' => '2010-03-26 11:38:57',
			'modified' => '2010-03-26 11:38:57'
		),
	);
}
?>