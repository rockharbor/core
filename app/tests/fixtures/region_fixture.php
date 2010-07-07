<?php
/* Region Fixture generated on: 2010-06-28 09:06:35 : 1277741435 */
class RegionFixture extends CakeTestFixture {
	var $name = 'Region';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'Orange County',
			'created' => '2010-03-17 08:45:58',
			'modified' => '2010-03-17 08:45:58'
		),
		array(
			'id' => 2,
			'name' => 'South County',
			'created' => '2010-05-04 08:54:08',
			'modified' => '2010-05-04 08:54:08'
		),
	);
}
?>