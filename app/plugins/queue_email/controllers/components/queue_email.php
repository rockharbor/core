<?php
/**
 * Queues controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       queue_email
 * @subpackage    queue_email.controllers.components
 */

/**
 * Includes
 */
App::import('Component', 'Email');
App::import('Model', 'QueueEmail.Queue');

/**
 * Queues Controller
 *
 * Stores emails in the database rather than sending them. Supports all
 * EmailComponent settings
 *
 * @package       queue_email
 * @subpackage    queue_email.controllers.components
 */
class QueueEmailComponent extends EmailComponent { 

/**
 * The original delivery setting
 *
 * @param string
 * @access protected
 */
	var $_oldDelivery = 'mail';

/**
 * Set to false if you don't want to queue an email
 *
 * @var boolean
 */
	var $queue = true;

/**
 * The Queue model
 *
 * @var Queue
 */
	var $Model = null;

/**
 * The initialize function
 *
 * Saves an instance of the Queue model for use
 *
 * @param Controller $controller
 * @param array $settings
 * @see Component::initialize()
 */
	function initialize(&$controller, $settings = array()) {
		$this->Model = ClassRegistry::init('Queue');
		parent::initialize($controller, $settings);
	}

/**
 * Forces the email to queue instead of send if `$this->queue = true`
 *
 * @param mixed $content Either an array of text lines, or a string with
 *		contents If you are rendering a template this variable will be sent
 *		to the templates as `$content`
 * @param string $template Template to use when sending email
 * @param string $layout Layout to use to enclose email body
 * @return boolean Success
 * @access public
 */
	function send($content = null, $template = null, $layout = null) {
		if ($this->queue === true) {
			$this->_oldDelivery = $this->delivery;
			$this->delivery = 'db';
		}
		$success = parent::send($content, $template, $layout);
		$this->delivery = $this->_oldDelivery;
		return $success;
	}

/**
 * Stores emails in the database instead of sending them immediately
 *
 * @return boolean Success
 */
	function _db() {
		$this->Model->create();
		return $this->Model->save(array(
			'to' => serialize($this->to),
			'cc' => serialize($this->cc),
			'bcc' => serialize($this->bcc),
			'from' => $this->from,
			'subject' => $this->subject,
			'delivery' => $this->_oldDelivery,
			'smtp_options' => serialize($this->smtpOptions),
			'message' => serialize($this->__message),
			'header' => serialize($this->__header),
		));
	}

/**
 * Interprets a saved Queue as an associative array by unserializing data
 *
 * @param array $message The message
 * @return array
 */
	function interpret($message = array()) {
		if (empty($message)) {
			return false;
		}
		if (isset($message['Queue'])) {
			$message = $message['Queue'];
		}
		foreach ($message as $field => &$value) {
			if (@unserialize($value) !== false) {
				$value = unserialize($value);
			}
		}
		return array(
			'Queue' => $message
		);
	}

/**
 * Override EmailComponent::_mail() to allow for testing
 *
 * @return boolean Success
 */
	function _mail() {
		return parent::_mail();
	}

/**
 * Override EmailComponent::_smtp() to allow for testing
 *
 * @return boolean Success
 */
	function _smtp() {
		return parent::_smtp();
	}
	
} 

?>