<?php
/**
 * EmailListener
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.libs.listeners
 */

/**
 * Includes
 */
App::import('Controller', 'App');

/**
 * EmailListener class
 *
 * Sends an email to the developer email
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.libs.listeners
 */
class EmailListener {

/**
 * Level mapping
 * 
 * @var array
 */
	var $levels = array(
		E_ERROR					=> 'E_ERROR',
		E_WARNING				=> 'E_WARNING',
		E_PARSE					=> 'E_PARSE',
		E_NOTICE					=> 'E_NOTICE',
		E_CORE_ERROR			=> 'E_CORE_ERROR',
		E_CORE_WARNING			=> 'E_CORE_WARNING',
		E_COMPILE_ERROR		=> 'E_COMPILE_ERROR',
		E_COMPILE_WARNING		=> 'E_COMPILE_WARNING',
		E_USER_ERROR			=> 'E_USER_ERROR',
		E_USER_WARNING			=> 'E_USER_WARNING',
		E_USER_NOTICE			=> 'E_USER_NOTICE',
		E_STRICT					=> 'E_STRICT',
		E_RECOVERABLE_ERROR	=> 'E_RECOVERABLE_ERROR',
		E_DEPRECATED			=> 'E_DEPRECATED',
	);

/**
 * Triggered when we're passed an error from the WhistleComponent
 *
 * @param array $error
 * @param array $configuration
 * @return null
 * @access public
 */
	function error($error, $configuration = array()) {
		extract($error);

		$body = '<strong>'.$this->levels[$level].'</strong>';
		$body .= '<br />'.$message;
		$body .= '<br />'.$file.' on line '.$line;

		$this->_getEmailer()->send(array(
			'to' => $this->_getEmailUsers(),
			'subject' => 'Application error!',
			'body' => $body
		));
	}

/**
 * Gets an instance of the email component (or custom one)
 *
 * @return object
 * @access protected
 */
	function _getEmailer() {
		if (!isset($this->Email)) {
			$this->Controller = new AppController();
			$this->Controller->constructClasses();
			$this->Controller->QueueEmail->initialize($this->Controller);
			$this->Email = $this->Controller->QueueEmail;
		}
		return $this->Email;
	}

/**
 * Gets a list of emails
 *
 * Currently only grabs the debug email set in app settings, but is set up to
 * allow for more
 *
 * @return array
 * @access protected
 */
	function _getEmailUsers() {
		$_emails = array();

		$devEmail = Core::read('debug_email');

		if (!is_null($devEmail) && !empty($devEmail)) {
			$_emails[] = $devEmail;
		}

		return $_emails;
	}
}
?>