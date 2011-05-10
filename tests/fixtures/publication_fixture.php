<?php
/* Publication Fixture generated on: 2010-06-28 09:06:55 : 1277741395 */
class PublicationFixture extends CakeTestFixture {
	var $name = 'Publication';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'link' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'ebulletin',
			'link' => '',
			'description' => 'Keep up to date on stuff happening at <strong>ROCK</strong>HARBOR.',
			'created' => '2010-05-06 08:35:55',
			'modified' => '2010-05-06 08:35:55'
		),
		array(
			'id' => 2,
			'name' => 'Family Ministry Update',
			'link' => '',
			'description' => 'It\'s in the name.',
			'created' => '2010-05-10 08:31:44',
			'modified' => '2010-05-10 08:31:44'
		),
	);
}
?>