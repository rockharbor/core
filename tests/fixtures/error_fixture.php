<?php
/* Error Fixture generated on: 2010-07-07 08:07:03 : 1278518043 */
class ErrorFixture extends CakeTestFixture {
	var $name = 'Error';

	var $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary'),
		'level' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 15),
		'file' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 200),
		'line' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 5),
		'message' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => '4c34a31b-0330-49e5-91fb-093cb36fd16a',
			'level' => 'Lorem ipsum d',
			'file' => 'Lorem ipsum dolor sit amet',
			'line' => 1,
			'message' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created' => '2010-07-07 08:54:03'
		),
	);
}
