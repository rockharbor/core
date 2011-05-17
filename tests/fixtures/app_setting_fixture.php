<?php
/* AppSetting Fixture generated on: 2010-09-10 09:09:37 : 1284136897 */
class AppSettingFixture extends CakeTestFixture {
	var $name = 'AppSetting';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'description' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'value' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1000),
		'type' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 45),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'general.church_name',
			'description' => 'The name of your church. Wrap it in a span tag with your own class for easy customization.',
			'created' => '2010-03-11 10:44:40',
			'modified' => '2010-08-17 14:38:33',
			'value' => '<span class=\"churchname\"><b>ROCK</b>HARBOR</span>',
			'type' => 'html'
		),
		array(
			'id' => 3,
			'name' => 'general.church_site_url',
			'description' => 'The church website url.',
			'created' => '2010-03-11 10:57:11',
			'modified' => '2010-03-11 10:57:11',
			'value' => 'http://www.rockharbor.org',
			'type' => 'string'
		),
		array(
			'id' => 4,
			'name' => 'users.user_document_limit',
			'description' => 'The maximum number of documents that can be uploaded to a user profile.',
			'created' => '2010-03-11 11:01:02',
			'modified' => '2010-03-11 11:01:02',
			'value' => '3',
			'type' => 'integer'
		),
		array(
			'id' => 8,
			'name' => 'general.site_name',
			'description' => 'The application display name.',
			'created' => '2010-03-11 13:04:01',
			'modified' => '2010-03-18 13:13:11',
			'value' => 'CORE',
			'type' => 'html'
		),
		array(
			'id' => 9,
			'name' => 'general.ebulletin',
			'description' => 'The publication that is the main church ebulletin.',
			'created' => '2010-03-18 00:00:00',
			'modified' => '2010-06-02 12:25:22',
			'value' => '1',
			'type' => 'Publication'
		),
		array(
			'id' => 10,
			'name' => 'development.core_developer',
			'description' => 'The main CORE developer. (App Setting test.)',
			'created' => '2010-03-18 00:00:00',
			'modified' => '2010-03-18 13:12:24',
			'value' => '1',
			'type' => 'User'
		),
		array(
			'id' => 11,
			'name' => 'notifications.site_email',
			'description' => 'The system email address. Used as a fallback for any non-customized email addresses.',
			'created' => '2010-03-18 00:00:00',
			'modified' => '2010-03-25 12:30:41',
			'value' => 'core@rockharbor.org',
			'type' => 'string'
		),
		array(
			'id' => 12,
			'name' => 'users.user_image_limit',
			'description' => 'The maximum number of images that can be uploaded to a user profile.',
			'created' => '2010-03-18 00:00:00',
			'modified' => '0000-00-00 00:00:00',
			'value' => '1',
			'type' => 'integer'
		),
		array(
			'id' => 13,
			'name' => 'development.debug_email',
			'description' => 'The email to send debug info, including test emails.',
			'created' => '2010-03-25 00:00:00',
			'modified' => '2010-03-25 12:30:27',
			'value' => '1',
			'type' => 'User'
		),
		array(
			'id' => 14,
			'name' => 'involvements.question_limit',
			'description' => 'The maximum number of questions an involvement can have.',
			'created' => '2010-04-09 00:00:00',
			'modified' => '2010-04-09 12:24:21',
			'value' => '5',
			'type' => 'integer'
		),
		array(
			'id' => 15,
			'name' => 'notifications.credit_card_email',
			'description' => 'The email address to send credit card payment receipts.',
			'created' => '0000-00-00 00:00:00',
			'modified' => '0000-00-00 00:00:00',
			'value' => 'jharris@rockharbor.org',
			'type' => 'string'
		),
		array(
			'id' => 16,
			'name' => 'notifications.email_subject_prefix',
			'description' => 'A prefix to put in front of the subject of each email sent through the app.',
			'created' => '2010-05-24 00:00:00',
			'modified' => '2010-05-24 00:00:00',
			'value' => 'CORE ::',
			'type' => 'string'
		),
		array(
			'id' => 17,
			'name' => 'notifications.activation_requests',
			'description' => 'The user to notify when account activation requests are sent.',
			'created' => '2010-05-27 00:00:00',
			'modified' => '2010-05-27 00:00:00',
			'value' => '1',
			'type' => 'User'
		),
		array(
			'id' => 18,
			'name' => 'notifications.ministry_content',
			'description' => 'The user to notify when Ministry content has been changed.',
			'created' => '2010-05-27 00:00:00',
			'modified' => '2010-05-27 00:00:00',
			'value' => '1',
			'type' => 'User'
		),
		array(
			'id' => 19,
			'name' => 'notifications.support_email',
			'description' => 'The address to send support email.',
			'created' => '2010-06-02 00:00:00',
			'modified' => '2010-06-02 00:00:00',
			'value' => 'jharris@rockharbor.org',
			'type' => 'string'
		),
		array(
			'id' => 20,
			'name' => 'notifications.campus_content',
			'description' => 'The user to notify when Campus content has been changed.',
			'created' => '2010-08-27 00:00:00',
			'modified' => '2010-08-27 00:00:00',
			'value' => '1',
			'type' => 'User'
		),
		array(
			'id' => 21,
			'name' => 'involvements.types',
			'description' => 'The types of involvement opportunities',
			'created' => '2010-09-10 00:00:00',
			'modified' => '2010-09-10 00:00:00',
			'value' => NULL,
			'type' => 'list'
		),
		array(
			'id' => 23,
			'name' => 'general.private_group',
			'description' => 'The group that a user must be above in order to see private Involvements and Ministries.',
			'created' => '2010-09-13 00:00:00',
			'modified' => '2010-09-13 00:00:00',
			'value' => 8,
			'type' => 'Group'
		),
		array(
			'id' => 24,
			'name' => 'users.default_image',
			'description' => 'The default user image.',
			'created' => '2010-09-13 00:00:00',
			'modified' => '2010-09-13 00:00:00',
			'value' => null,
			'type' => 'image'
		),
		array(
			'id' => 25,
			'name' => 'users.default_icon',
			'description' => 'The default user icon.',
			'created' => '2010-09-13 00:00:00',
			'modified' => '2010-09-13 00:00:00',
			'value' => null,
			'type' => 'image'
		),
	);
}
?>