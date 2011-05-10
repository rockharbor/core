<?php
/* School Fixture generated on: 2010-06-28 09:06:11 : 1277741471 */
class PaginateTestFixture extends CakeTestFixture {
	var $name = 'PaginateTest';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'A Paginated Thing',
			'description' => 'This is something that should be paginated',
			'created' => '2010-05-20 00:00:00',
			'modified' => '2010-05-20 00:00:00'
		),
		array(
			'id' => 2,
			'name' => 'The CORE Awesomeness',
			'description' => 'What are these things?',
			'created' => '2010-05-21 00:00:00',
			'modified' => '2010-05-22 00:00:00'
		),
		array(
			'id' => 3,
			'name' => 'Back to the Future',
			'description' => 'A movie about lightning and its effect on cars',
			'created' => '2010-04-20 00:00:00',
			'modified' => '2010-04-20 00:00:00'
		)
	);
}
?>