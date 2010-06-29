<?php
/* PublicationsUser Fixture generated on: 2010-06-28 09:06:12 : 1277741412 */
class PublicationsUserFixture extends CakeTestFixture {
	var $name = 'PublicationsUser';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'publication_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 35,
			'publication_id' => 1,
			'user_id' => 1
		),
	);
}
?>