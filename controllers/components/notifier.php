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
 * - string $type The notification type (invitation or default)
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
		$user = $this->User->read(null, $options['to']);
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
			'attachments' => array()
		);
		$options = array_merge($default, $options);
		extract($options);

		$this->User->contain(array('Profile'));
		// set system defaults if no 'from' user
		if (!$from) {
			$from = array(
				'Profile' => array(
					'name' => Core::read('general.site_name_tagless'),
					'primary_email' => Core::read('notifications.site_email')
				)
			);
		} else {
			$from = $this->User->read(null, $from);
		}
		// send to debug email if 
		if (Configure::read('debug') > 0) {
			$this->Controller->set('_originalUser', $user);
			$user = array(
				'Profile' => array(
					'name' => $this->Controller->activeUser['Profile']['name'],
					'primary_email' => $this->Controller->activeUser['Profile']['primary_email']
				)
			);
			$layout = 'debug';
		}
		$this->QueueEmail->smtpOptions = $this->smtp;
		$this->QueueEmail->delivery = 'smtp';
		$this->QueueEmail->sendAs = 'html';
		$this->QueueEmail->layout = $layout;
		$this->QueueEmail->template = $template;
		$this->QueueEmail->attachments = $attachments;
		$this->QueueEmail->from = $from['Profile']['name'].' <'.$from['Profile']['primary_email'].'>';
		$this->QueueEmail->subject = Core::read('notifications.email_subject_prefix').' '.$subject;
		if (!empty($user['Profile']['primary_email']) && !empty($user['Profile']['name'])) {
			$this->QueueEmail->to = $user['Profile']['name'].' <'.$user['Profile']['primary_email'].'>';
		} else {
			return false;
		}
		if (!$this->QueueEmail->send($body)) {
			CakeLog::write('smtp', $this->QueueEmail->smtpError);
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
 * - string $type The body of the message. Usually the template takes care of this.
 *
 * @param array $user The user and profile information to notify
 * @param array $options Array of options
 * @return boolean Success
 * @access private
 */ 	
	function _save($user, $options = array()) {
		$defaults = array(
			'type' => 'default'
		);
		$options = array_merge($defaults, $options);
		extract($options);

		$content = $this->_render($template);
		if ($content === false) {
			return false;
		}
		$data = array(
			'Notification' => array(
				'user_id' => $user['User']['id'],
				'body' => $content,
				'type' => $type,
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

}

?>