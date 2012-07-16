<?php
/* PublicationsUser Fixture generated on: 2010-06-28 09:06:12 : 1277741412 */
class PublicationsUserFixture extends CakeTestFixture {
	var $name = 'PublicationsUser';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'publication_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'join_key' => array('column' => array('publication_id', 'user_id'), 'unique' => 1), 'publication_key' => array('column' => 'publication_id', 'unique' => 0), 'user_key' => array('column' => 'user_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'publication_id' => 1,
			'user_id' => 1
		),
		array(
			'id' => 2,
			'publication_id' => 2,
			'user_id' => 1
		),
		array(
			'id' => 3,
			'publication_id' => 2,
			'user_id' => 2
		)
	);
}
