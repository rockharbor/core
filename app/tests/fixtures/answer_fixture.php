<?php
/* Answer Fixture generated on: 2010-06-28 09:06:43 : 1277741203 */
class AnswerFixture extends CakeTestFixture {
	var $name = 'Answer';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'roster_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'question_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
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