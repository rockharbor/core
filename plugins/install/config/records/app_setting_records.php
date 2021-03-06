<?php

class AppSettingRecords extends Records {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'AppSetting';

/**
 * Record to import upon install
 *
 * @var array
 */
	var $records = array(
		array(
			'id' => 1,
			'name' => 'general.church_name',
			'description' => 'The name of your church.',
			'value' => 'Church',
			'type' => 'html'
		),
		array(
			'id' => 2,
			'name' => 'general.church_site_url',
			'description' => 'The church website url.',
			'value' => 'http://example.com',
			'type' => 'string'
		),
		array(
			'id' => 3,
			'name' => 'users.user_document_limit',
			'description' => 'The maximum number of documents that can be uploaded to a user profile.',
			'value' => '3',
			'type' => 'integer'
		),
		array(
			'id' => 4,
			'name' => 'general.site_name',
			'description' => 'The application display name.',
			'value' => 'CORE',
			'type' => 'html'
		),
		array(
			'id' => 6,
			'name' => 'notifications.site_email',
			'description' => 'The system email address. Used as a fallback for any non-customized email addresses.',
			'value' => 'core@example.com',
			'type' => 'string'
		),
		array(
			'id' => 7,
			'name' => 'users.user_image_limit',
			'description' => 'The maximum number of images that can be uploaded to a user profile.',
			'value' => '1',
			'type' => 'integer'
		),
		array(
			'id' => 8,
			'name' => 'development.debug_email',
			'description' => 'The email to send debug info, including test emails.',
			'value' => 'debug@example.com',
			'type' => 'string'
		),
		array(
			'id' => 9,
			'name' => 'involvements.question_limit',
			'description' => 'The maximum number of questions an involvement can have.',
			'value' => '5',
			'type' => 'integer'
		),
		array(
			'id' => 10,
			'name' => 'notifications.credit_card_email',
			'description' => 'The email address to send credit card payment receipts.',
			'value' => 'payments@example.com',
			'type' => 'string'
		),
		array(
			'id' => 12,
			'name' => 'notifications.activation_requests',
			'description' => 'The user to notify when account activation requests are sent.',
			'value' => '1',
			'type' => 'User'
		),
		array(
			'id' => 13,
			'name' => 'notifications.ministry_content',
			'description' => 'The user to notify when Ministry content has been changed.',
			'value' => '1',
			'type' => 'User'
		),
		array(
			'id' => 14,
			'name' => 'notifications.support_email',
			'description' => 'The address to send support email.',
			'value' => 'support@example.com',
			'type' => 'string'
		),
		array(
			'id' => 15,
			'name' => 'notifications.campus_content',
			'description' => 'The user to notify when Campus content has been changed.',
			'value' => '1',
			'type' => 'User'
		),
		array(
			'id' => 16,
			'name' => 'general.private_group',
			'description' => 'User must be above this group in order to see private Involvements and Ministries, and Flagged users.',
			'value' => 8,
			'type' => 'Group'
		),
		array(
			'id' => 17,
			'name' => 'users.default_image',
			'description' => 'The default image for a user.',
			'value' => null,
			'type' => 'image'
		),
		array(
			'id' => 18,
			'name' => 'users.default_icon',
			'description' => 'The default icon for a user, if different than the default image.',
			'value' => null,
			'type' => 'image'
		),
		array(
			'id' => 19,
			'name' => 'general.promoted_item_limit',
			'description' => 'The maximum amount of promoted items to show on the Profile page.',
			'value' => 2,
			'type' => 'integer'
		),
		array(
			'id' => 20,
			'name' => 'general.tracking_code',
			'description' => 'Analytic tracking JavaScript code for the site (i.e., Google Analytics, StatCounter, etc.). Exclude script tags.',
			'value' => null,
			'type' => 'html'
		),
		array(
			'id' => 21,
			'name' => 'involvements.involvement_document_limit',
			'description' => 'The maximum number of documents that can be uploaded to an Involvement Opportunity.',
			'value' => 5,
			'type' => 'integer'
		),
		array(
			'id' => 22,
			'name' => 'ministries.ministry_document_limit',
			'description' => 'The maximum number of documents that can be uploaded to a Ministry.',
			'value' => 5,
			'type' => 'integer'
		),
		array(
			'id' => 23,
			'name' => 'sys_emails.sys_email_document_limit',
			'description' => 'The maximum number of documents that can attached to an email.',
			'value' => 1,
			'type' => 'integer'
		),
		array(
			'id' => 24,
			'name' => 'sys_emails.system_subject_prefix',
			'description' => 'A prefix to put in front of the subject of each system email sent through the app.',
			'value' => '[core]',
			'type' => 'string'
		),
		array(
			'id' => 25,
			'name' => 'sys_emails.subject_prefix',
			'description' => 'A prefix to put in front of the subject of each user email sent through the app.',
			'value' => '',
			'type' => 'string'
		),
	);

}

