<?php
/**
 * Imports
 */
App::import('Lib', 'Referee.EmailRefereeListener');
require_once CONFIGS . 'email.php';

/**
 * EmailListener
 *
 * Provides functionality for logging errors to email.
 */
class EmailListener extends EmailRefereeListener {

	/**
	 * Constructor
	 *
	 * Overridden to instantiate custom mailer component
	 *
	 * @param   array $config
	 * @return  void
	 */
	public function __construct($config = array()) {
		$emailConfig = new EmailConfig();
		
		$config['mailerConfig']['to'] = Core::read('development.debug_email');
		
		if ($emailConfig->debug['transport'] == 'Smtp') {
			$default = array(
				'host' => 'localhost',
				'port' => 25,
				'timeout' => 30
			);
			$config = array_merge($default, $config);
			$smtp = array_intersect_key($config, array('host' => null, 'port' => null, 'timeout' => null, 'username' => null, 'password' => null));
			
			$config['mailerConfig']['smtpOptions'] = $smtp;
		}
		
		parent::__construct($config);
	}

}