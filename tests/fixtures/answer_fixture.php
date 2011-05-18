<?php
/* Answer Fixture generated on: 2010-06-28 09:06:43 : 1277741203 */
class AnswerFixture extends CakeTestFixture {
	var $name = 'Answer';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'roster_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'question_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 250),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'roster_key' => array('column' => 'roster_id', 'unique' => 0), 'question_key' => array('column' => 'question_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'roster_id' => 1,
			'question_id' => 1,
			'description' => 'Red?',
			'created' => '2010-05-04 07:33:03',
			'modified' => '2010-05-04 07:33:03'
		),
		array(
			'id' => 2,
			'roster_id' => 1,
			'question_id' => 2,
			'description' => 'Here\'s an answer',
			'created' => '2010-04-22 13:16:40',
			'modified' => '2010-04-22 13:16:40'
		)
	);
}
?>