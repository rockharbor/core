<?php
/* Classification Fixture generated on: 2010-06-28 09:06:26 : 1277741126 */
class ClassificationFixture extends CakeTestFixture {
	var $name = 'Classification';

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
			'name' => 'I\'m A Weekend Attender',
			'created' => '2010-01-20 08:18:29',
			'modified' => '2010-03-17 14:40:09'
		),
		array(
			'id' => 3,
			'name' => 'I\'m A Friend But Don\'t Attend',
			'created' => '2010-01-20 08:18:41',
			'modified' => '2010-01-20 08:18:41'
		),
		array(
			'id' => 4,
			'name' => 'Wherever the wind blows',
			'created' => '2010-02-25 11:37:35',
			'modified' => '2010-02-25 11:37:35'
		),
	);
}
?>