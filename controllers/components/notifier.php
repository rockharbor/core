<?php
/**
 * Notifier component class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers.components
 */

/**
 * Includes
 */
App::import('View', 'view');
require_once CONFIGS . 'email.php';

/**
 * Notifier Component
 *
 * This component is used for sending notifications via email and saving them
 * to the database using a model.
 *
 * @todo Email based on user preference (set in controller?)
 * @package       core
 * @subpackage    core.app.controllers.components
 */
class NotifierComponent extends Object {

/**
 * Additional components needed by this component
 *
 * @var array
 */
	public $components = array('QueueEmail.QueueEmail');

/**
 * Whether or not the component is enabled
 *
 * @var boolean
 */
	public $enabled = true;

/**
 * Initialize component
 *
 * @param object $controller Instantiating controller
 * @param array $settings Default settings
 */
	public function initialize(&$controller, $settings = array()) {
		$this->Config = new EmailConfig();
		$this->Controller =& $controller;
		$this->User = ClassRegistry::init('User');
		$this->Notification = ClassRegistry::init('Notification');
		$this->Invitation = ClassRegistry::init('Invitation');
		$this->_set($settings);
	}

/**
 * Sends an email and saves a notification
 *
 * ### Options:
 * - integer $from The User id of the sender
 * - mixed $to The User id to send to
 * - string $subject The subject line
 * - string $template The template to load (view element for notification and email)
 *
 * @param array $options Array of options
 * @param string $type The type of notification to send (notification, email, both)
 * @return boolean Success
 */
	public function notify($options = array(), $type = 'both') {
		if (!$this->enabled || !isset($options['to'])) {
			return false;
		}
		$user = $this->_normalizeUser($options['to']);
		if (!$user) {
			return false;
		}

		$email = $notification = true;
		switch (strtolower($type)) {
			case 'email':
				$email = $this->_send($user, $options);
			break;
			case 'notification':
				$notification = $this->_save($user, $options);
			break;
			default:
				$email = $this->_send($user, $options);
				$notification = $this->_save($user, $options);
			break;
		}
		return $notification && $email;
	}

/**
 * Sends an invitation to one or more people
 *
 * If sending to multiple people (`$cc`), a single invitation will be created but each
 * of the users will be notified. Once any one of those users chooses an action,
 * the invitation will no longer show up in any of the users' list of notifications.
 *
 * ### Options:
 * - mixed $to The user id to send to
 * - array $cc Users to copy the invitation to
 * - string $template The template (view element)
 * - string $confirm The confirmation link
 * - string $deny The denial link
 *
 * @param array $options Array of options
 * @return boolean Success
 */
	public function invite($options = array()) {
		$_defaults = array(
			'to' => false,
			'confirm' => false,
			'deny' => false,
			'template' => false
		);
		$options = array_merge($_defaults, $options);
		if (!$this->enabled || !$options['to'] || !$options['confirm'] || !$options['deny']) {
			return false;
		}
		$content = $this->_render($options['template']);
		if ($content === false) {
			return false;
		}
		$data = array(
			'Invitation' => array(
				'user_id' => $options['to'],
				'body' => $content,
				'confirm_action' => $options['confirm'],
				'deny_action' => $options['deny']
			)
		);
		if (isset($options['cc']) && !empty($options['cc'])) {
			if (!is_array($options['cc'])) {
				$options['cc'] = array($options['cc']);
			}
			$data['CC'] = array('CC' => array());
			foreach ($options['cc'] as $cc) {
				$data['CC']['CC'][] = $cc;
			}
		}
		$this->Invitation->create();
		return $this->Invitation->saveAll($data);
	}

/**
 * Queues an email and renders it using the EmailView class
 *
 * If $from is not defined, it sends the email from the site instead, using
 * configured options (see AppSettings)
 *
 * ### Options:
 * - integer $from The User id of the sender
 * - integer $to The User id to send to
 * - string $subject The subject line
 * - string $template The template to load (view element)
 * - string $layout The layout to load
 * - string $body The body of the message. Usually the template takes care of this.
 *
 * @param array $user The user and profile information to send to
 * @param array $options Array of options
 * @return boolean Success
 */
	protected function _send($user, $options = array()) {
		$_originalView = $this->QueueEmail->Controller->view;
		$this->QueueEmail->Controller->view = 'Email';

		$config = Configure::read('debug') == 0 ? $this->Config->default : $this->Config->debug;

		$this->QueueEmail->reset();
		$default = array(
			'from' => null,
			'subject' => 'New notification',
			'template' => 'default',
			'layout' => 'notifications',
			'body' => null,
			'attachments' => array(),
			'queue' => true
		);
		$options = array_merge($default, $options);
		extract($options);

		// set system defaults if no 'from' user
		$from = $this->_normalizeUser($from);

		// set default preferences
		if (!isset($this->Controller->viewVars['include_greeting'])) {
			$this->Controller->set('include_greeting', true);
		}
		if (!isset($this->Controller->viewVars['include_signoff'])) {
			$this->Controller->set('include_signoff', true);
		}

		$this->Controller->set('toUser', $user);

		if ($config['transport'] == 'Smtp') {
			$default = array(
				'host' => 'localhost',
				'port' => 25,
				'timeout' => 30
			);
			$config = array_merge($default, $config);
			$smtp = array_intersect_key($config, array('host' => null, 'port' => null, 'timeout' => null, 'username' => null, 'password' => null));
			$this->QueueEmail->smtpOptions = $smtp;
		}
		$this->QueueEmail->delivery = strtolower($config['transport']);
		$this->QueueEmail->sendAs = 'html';
		$this->QueueEmail->layout = $layout;
		$this->QueueEmail->template = $template;
		$this->QueueEmail->attachments = $attachments;
		$this->QueueEmail->queue = $queue;
		/*
		 * Due to Yahoo! and AOL's new, stricter DMARC policy, From addresses
		 * must now be strictly SPF or DKIM compliant. We'll set From to be the system email
		 * and Reply-To and Return-Path to the sender's email
		 * See also: http://yahoomail.tumblr.com/post/82426900353/yahoo-dmarc-policy-change-what-should-senders-do
		 */
		$siteNameTagless = Core::read('general.site_name_tagless');
		$siteEmail = Core::read('notifications.site_email');
		$this->QueueEmail->from = (($from['Profile']['name'] == $siteNameTagless) ?
				$siteNameTagless : $from['Profile']['name'] . ' via ' . $siteNameTagless) .
				' <' . $siteEmail . '>';
		$this->QueueEmail->replyTo = $this->QueueEmail->return =
				$from['Profile']['name'].' <'.$from['Profile']['primary_email'].'>';

		$prefixKey = ($from['User']['id'] === 0) ? 'system_subject_prefix' : 'subject_prefix';
		$this->QueueEmail->subject = Core::read("sys_emails.$prefixKey").$subject;

		$failed = false;
		if (!empty($user['Profile']['primary_email']) && !empty($user['Profile']['name'])) {
			$this->QueueEmail->to = $user['Profile']['name'].' <'.$user['Profile']['primary_email'].'>';
		} else {
			$failed = true;
		}
		if (!$failed && !$this->QueueEmail->send($body)) {
			if ($queue) {
				// if it's queued, the error will result from a failed database save
				CakeLog::write('smtp', $this->QueueEmail->getDataSource()->lastError());
			} else {
				// if it's not queued, log the smtp error
				CakeLog::write('smtp', $this->QueueEmail->smtpError);
			}
			$failed = true;
		}

		$this->QueueEmail->Controller->view = $_originalView;
		if ($failed) {
			return false;
		}

		if ($queue && $this->QueueEmail->Model->id) {
			// save the ids of the users this was to and from
			$this->QueueEmail->Model->save(array(
				'Queue' => array(
					'id' => $this->QueueEmail->Model->id,
					'to_id' => $user['User']['id'],
					'from_id' => $from['User']['id']
				)
			), array('validate' => false));
		}

		return true;
	}

/**
 * Saves the notification to the database
 *
 * ### Options:
 * - integer $to The User id of the sender
 * - string $template The notification template
 *
 * @param array $user The user and profile information to notify
 * @param array $options Array of options
 * @return boolean Success
 */
	protected function _save($user, $options = array()) {
		extract($options);

		$content = $this->_render($template);
		if ($content === false) {
			return false;
		}
		$data = array(
			'Notification' => array(
				'user_id' => $user['User']['id'],
				'body' => $content,
				'read' => false,
			)
		);
		$this->Notification->create();
		return $this->Notification->save($data);
	}

/**
 * Renders the notification template
 *
 * @param string $template The template to render
 * @return string Rendered content
 */
	protected function _render($template) {
		$View = new View($this->Controller, false);
		$View->layout = 'notification';
		list($plugin, $template) = pluginSplit($template);
		$content = $View->element('notification' . DS . $template, compact('plugin'), true);
		$content = $View->renderLayout($content);
		return $content;
	}

/**
 * Normalizes a user argument for the purposes of sending an email
 *
 * @param mixed $user Can be a user id, an email address, or a user array with
 * the proper keys
 * @return array Array containing user 'name' and 'primary_email' values
 */
	protected function _normalizeUser($user = null) {
		if ($user === null) {
			$user = array(
				'Profile' => array(
					'name' => Core::read('general.site_name_tagless'),
					'primary_email' => Core::read('notifications.site_email')
				)
			);
		}
		if (is_numeric($user)) {
			$user = $this->User->find('first', array(
				'fields' => array(
					'id'
				),
				'conditions' => array(
					'User.id' => $user,
					'User.active' => true
				),
				'contain' => array(
					'Profile' => array(
						'fields' => array('primary_email', 'first_name', 'last_name')
					)
				)
			));
			if (!empty($user)) {
				$user['Profile']['name'] = $user['Profile']['first_name'].' '.$user['Profile']['last_name'];
			}
		} elseif (is_string($user)) {
			$user = array(
				'User' => array(
					'id' => 0
				),
				'Profile' => array(
					'first_name' => $user,
					'last_name' => '',
					'name' => $user,
					'primary_email' => $user
				)
			);
		} else {
			if (!isset($user['Profile']['name']) || !isset($user['Profile']['primary_email'])) {
				return null;
			}
			$default = array(
				'User' => array(
					'id' => 0
				),
				'Profile' => array(
					'first_name' => $user['Profile']['name'],
					'last_name' => ''
				)
			);
			$user = array_merge_recursive($default, $user);
		}
		return $user;
	}

}

