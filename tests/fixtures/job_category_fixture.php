<?php
/* JobCategory Fixture generated on: 2010-06-28 09:06:35 : 1277741135 */
class JobCategoryFixture extends CakeTestFixture {
	var $name = 'JobCategory';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 45),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'IT/software developments',
			'created' => '2010-01-20 08:19:22',
			'modified' => '2010-03-30 14:20:49'
		),
		array(
			'id' => 2,
			'name' => 'law enforcement/security',
			'created' => '2010-01-20 08:19:27',
			'modified' => '2010-02-11 11:59:34'
		),
		array(
			'id' => 20,
			'name' => 'internet',
			'created' => '2010-03-30 14:23:52',
			'modified' => '2010-03-30 14:28:07'
		),
	);
}
?>