<?php
/* MinistriesRev Fixture generated on: 2010-06-28 09:06:21 : 1277741361 */
class MinistriesRevFixture extends CakeTestFixture {
	var $name = 'MinistriesRev';

	var $fields = array(
		'version_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'version_created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'campus_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'group_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'version_id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
	);
}
?>