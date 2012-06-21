<?php
/* Question Fixture generated on: 2010-06-28 09:06:19 : 1277741419 */
class QuestionFixture extends CakeTestFixture {
	var $name = 'Question';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'involvement_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'order' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1000),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'involvement_key' => array('column' => 'involvement_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'involvement_id' => 1,
			'order' => 1,
			'description' => 'What is the color blue?',
			'created' => '2010-04-13 09:33:14',
			'modified' => '2010-04-13 09:33:14'
		),
		array(
			'id' => 2,
			'involvement_id' => 1,
			'order' => 2,
			'description' => 'another question!',
			'created' => '2010-04-09 13:05:40',
			'modified' => '2010-04-09 13:16:14'
		),
		array(
			'id' => 3,
			'involvement_id' => 5,
			'order' => 1,
			'description' => 'Who was the all time leader in sports?',
			'created' => '2010-04-09 13:05:40',
			'modified' => '2010-04-09 13:16:14'
		)
	);
}
?>