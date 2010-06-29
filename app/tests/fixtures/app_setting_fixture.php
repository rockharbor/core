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
			'id' => 3,
			'name' => 'church_site_url',
			'description' => 'The church website url.',
			'created' => '2010-03-11 10:57:11',
			'modified' => '2010-03-11 10:57:11',
			'value' => 'http://www.rockharbor.org',
			'model' => '',
			'html' => 0
		),
		array(
			'id' => 4,
			'name' => 'user_document_limit',
			'description' => 'The maximum number of documents that can be uploaded to a user profile.',
			'created' => '2010-03-11 11:01:02',
			'modified' => '2010-03-11 11:01:02',
			'value' => '3',
			'model' => '',
			'html' => 0
		),
		array(
			'id' => 8,
			'name' => 'site_name',
			'description' => 'The application display name.',
			'created' => '2010-03-11 13:04:01',
			'modified' => '2010-03-18 13:13:11',
			'value' => 'CORE',
			'model' => '',
			'html' => 1
		),
		array(
			'id' => 9,
			'name' => 'ebulletin',
			'description' => 'The publication that is the main church ebulletin.',
			'created' => '2010-03-18 00:00:00',
			'modified' => '2010-06-02 12:25:22',
			'value' => '1',
			'model' => 'Publication',
			'html' => 0
		),
		array(
			'id' => 10,
			'name' => 'core_developer',
			'description' => 'The main CORE developer. (App Setting test.)',
			'created' => '2010-03-18 00:00:00',
			'modified' => '2010-03-18 13:12:24',
			'value' => '1',
			'model' => 'User',
			'html' => 0
		),
		array(
			'id' => 11,
			'name' => 'site_email',
			'description' => 'The system email address. Used as a fallback for any non-customized email addresses.',
			'created' => '2010-03-18 00:00:00',
			'modified' => '2010-03-25 12:30:41',
			'value' => 'core@rockharbor.org',
			'model' => '',
			'html' => 0
		),
		array(
			'id' => 12,
			'name' => 'user_image_limit',
			'description' => 'The maximum number of images that can be uploaded to a user profile.',
			'created' => '2010-03-18 00:00:00',
			'modified' => '0000-00-00 00:00:00',
			'value' => '1',
			'model' => '',
			'html' => 0
		),
		array(
			'id' => 13,
			'name' => 'debug_email',
			'description' => 'The email to send debug info, including test emails.',
			'created' => '2010-03-25 00:00:00',
			'modified' => '2010-03-25 12:30:27',
			'value' => 'jharris@rockharbor.org',
			'model' => '',
			'html' => 0
		),
		array(
			'id' => 14,
			'name' => 'involvement_question_limit',
			'description' => 'The maximum number of questions an involvement can have.',
			'created' => '2010-04-09 00:00:00',
			'modified' => '2010-04-09 12:24:21',
			'value' => '5',
			'model' => '',
			'html' => 0
		),
	);
}
?>