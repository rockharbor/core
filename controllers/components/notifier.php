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
	var $components = array('QueueEmail.QueueEmail');

/**
 * Smtp settings
 *
 * @var array
 */
	var $smtp = array(
		'port'=>'25',
		'timeout'=>'30',
		'host' => 'mail.rockharbor.org'
	);

/**
 * Whether or not the component is enabled
 *
 * @var boolean
 */
	var $enabled = true;

/**
 * Initialize component
 *
 * @param object $controller Instantiating controller
 * @param array $settings Default settings
 * @access public
 */
	function initialize(&$controller, $settings = array()) {
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
 * @access public
 */ 
	function notify($options = array(), $type = 'both') {		
		if (!$this->enabled || !isset($options['to'])) {
			return false;
		}
		$this->User->contain(array('Profile'));
		$user = $this->User->find('first', array(
			'conditions' => array(
				'User.active' => true,
				'User.id' => $options['to']
			)
		));
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
	function invite($options = array()) {
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
 * Queues an email
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
 * @access protected
 */
	function _send($user, $options = array()) {
		$this->QueueEmail->reset();
		$default = array(
			'from' => null,
			'subject' => '',
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
	
		$this->Controller->set('toUser', $user);
		$this->QueueEmail->smtpOptions = $this->smtp;
		$this->QueueEmail->delivery = 'smtp';
		$this->QueueEmail->sendAs = 'html';
		$this->QueueEmail->layout = $layout;
		$this->QueueEmail->template = $template;
		$this->QueueEmail->attachments = $attachments;
		$this->QueueEmail->queue = $queue;
		$this->QueueEmail->from = $from['Profile']['name'].' <'.$from['Profile']['primary_email'].'>';
		$this->QueueEmail->subject = Core::read('notifications.email_subject_prefix').' '.$subject;
		if (!empty($user['Profile']['primary_email']) && !empty($user['Profile']['name'])) {
			$this->QueueEmail->to = $user['Profile']['name'].' <'.$user['Profile']['primary_email'].'>';
		} else {
			return false;
		}
		if (!$this->QueueEmail->send($body)) {
			CakeLog::write('smtp', $this->QueueEmail->smtpError);
			CakeLog::write('smtp', print_r($this->QueueEmail, true));
			return false;
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
 * @access private
 */ 	
	function _save($user, $options = array()) {
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
 * @access private
 */ 	
	function _render($template) {	
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
	function _normalizeUser($user = null) {
		if ($user === null) {
			$user = array(
				'Profile' => array(
					'name' => Core::read('general.site_name_tagless'),
					'primary_email' => Core::read('notifications.site_email')
				)
			);
		} elseif (is_numeric($user)) {
			$user = $this->User->find('first', array(
				'fields' => array(
					'id'
				),
				'conditions' => array(
					'User.id' => $user
				),
				'contain' => array(
					'Profile' => array(
						'fields' => array('primary_email', 'first_name', 'last_name')
					)
				)
			));
			$user = array(
				'User' => array(
					'id' => $user['User']['id']
				),
				'Profile' => array(
					'name' => $user['Profile']['first_name'].' '.$user['Profile']['last_name'],
					'primary_email' => $user['Profile']['primary_email']
				)
			);
		} elseif (is_string($user)) {
			$user = array(
				'Profile' => array(
					'name' => $user,
					'primary_email' => $user
				)
			);
		
		} else {
			if (!isset($user['Profile']['name']) || !isset($user['Profile']['primary_email'])) {
				$user = null;
			}
		}
		return $user;
	}

}

?>