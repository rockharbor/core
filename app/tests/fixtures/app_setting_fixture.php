<?php
/* AppSetting Fixture generated on: 2010-06-28 09:06:53 : 1277741213 */
class AppSettingFixture extends CakeTestFixture {
	var $name = 'AppSetting';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'description' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'value' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'model' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 45),
		'html' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'church_name',
			'description' => 'The name of your church. Wrap it in a span tag with your own class for easy customization.',
			'created' => '2010-03-11 10:44:40',
			'modified' => '2010-03-25 08:07:32',
			'value' => '<span class=\"churchname\"><b>ROCK</b>HA<i>R</i>BOR</span>',
			'model' => '',
			'html' => 1
		),
		array(
			'id' => 2,
			'name' => 'ebulletin',
			'description' => 'The publication that is the main church ebulletin.',
			'created' => '2010-03-18 00:00:00',
			'modified' => '2010-06-02 12:25:22',
			'value' => '1',
			'model' => 'Publication',
			'html' => 0
		)
	);
}
?>