<?php
/**
 * Sys Email Component class
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers.components
 */

/**
 * Includes
 */
App::import('Component', 'Email');

/**
 * SysEmailComponent
 *
 * This component is used for sending notifications via email and saving them
 * to the database using a model.
 *
 * @package       core
 * @subpackage    core.app.controllers.components
 */
class QueueEmailComponent extends EmailComponent {

/**
 * Whether or not the component should send mail
 * 
 * @var boolean 
 */
	var $enabled = true;

/**
 * Sends an email
 *
 * If $from is not defined, it sends the email from the site instead, using
 * configured options (see AppSettings)
 *
 * ### Options:
 * - integer $from The User id of the sender
 * - mixed $to List of User ids to send to (can be one)
 * - string $subject The subject line
 * - string $template The template to load (view element)
 * - string $layout The layout to load
 * - string $body The body of the message. Usually the template takes care of this.
 *
 * @return boolean Success
 * @access protected
 * @todo Make it queue in database
 */
	function send($options = array()) {
		if (!$this->enabled) {
			return true;
		}

		$this->reset();
		$User =& ClassRegistry::init('User');
		$User->contain(array('Profile'));

		$default = array(
			'from' => null,
			'to' => array(),
			'subject' => '',
			'template' => 'default',
			'layout' => 'default',
			'body' => null
		);

		$options = array_merge($default, $options);
		extract($options);

		$smtp = array(
			'port'=>'25',
			'timeout'=>'30',
			'host' => 'mail.rockharbor.org'
		);

		$systemEmail = array(
			'Profile' => array(
				'name' => Core::read('general.site_name_tagless'),
				'primary_email' => Core::read('notifications.site_email')
			)
		);

		// set system defaults if no 'from' user
		if (!$from) {
			$from = $systemEmail;
		} else {
			$from = $User->read(null, $from);
		}

		$this->smtpOptions = $smtp;
		$this->delivery = 'smtp';
		$this->sendAs = 'html';
		$this->layout = $layout;
		$this->template = $template;

		// check if they just sent one user
		if (!is_array($to)) {
			$to = array($to);
		}

		$to = $User->find('all', array(
			'conditions' => array(
				'User.id' => $to,
				'User.active' => true
			),
			'contain' => array(
				'Profile'
			)
		));

		$bcc = array();
		foreach ($to as $toUser) {
			if (!empty($toUser['Profile']['primary_email']) && !empty($toUser['Profile']['name'])) {
				$bcc[] = $toUser['Profile']['name'].' <'.$toUser['Profile']['primary_email'].'>';
			}
		}
		$this->from = $from['Profile']['name'].' <'.$from['Profile']['primary_email'].'>';

		$this->bcc = $bcc;
		$this->to = $systemEmail['Profile']['name'].' <'.$systemEmail['Profile']['primary_email'].'>';

		$this->subject = Core::read('notifications.email_subject_prefix').' '.$subject;

		if (!parent::send($body)) {
			CakeLog::write('smtp', $this->smtpError);
			return false;
		} else {
			return true;
		}
	}

}
?>