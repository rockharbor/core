<?php
/**
 * Queue sending shell.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       queue_email
 * @subpackage    queue_email.vendors.shells
 */

/**
 * Includes
 */
App::import('Core', 'Controller');
App::import('Component', 'Email');
App::import('Model', 'Queue');

/**
 * Queue Sender Shell
 *
 * Sends emails from the queue
 *
 * @package       queue_email
 * @subpackage    queue_email.vendors.shells
 */
class QueueSenderShell extends Shell { 

/**
 * Default batch size. Customize this by setting 
 * {{{
 * Configure::write('QueueEmail.batchSize', 25);
 * }}}
 * 
 * @var integer
 */
	var $limit = 50;

/**
 * Startup function
 */
	function startup() {
		$this->Controller = new Controller();
		$this->Email =& new EmailComponent();
		$this->Email->initialize($this->Controller);
		$this->Queue =& ClassRegistry::init('Queue');
	}

/**
 * Override main() for help message hook
 *
 * @access public
 */
	function main() {
		$out  = "Available QueueEmail commands:"."\n";
		$out .= "\t - send \$id -batchSize \$size\n";
		$out .= "\t - help\n\n";
		$out .= "For help, run the 'help' command.  For help on a specific command, run 'help <command>'";
		$this->out($out);
	}

/**
 * Shows help for the shell commands
 *
 * @access public
 */
	function help() {
		$out  = "Usage: cake queue_email <command>"."\n";
		$out .= "-----------------------------------------------\n";

		$command = $this->args[0];

		if (empty($command)) {
			$out  = "Available QueueEmail commands:"."\n";
			$out .= "\t - send \$id -batchSize \$size\n";
			$out .= "\t - help\n\n";
			$out .= "For help, run the 'help' command.  For help on a specific command, run 'help <command>'";
			$this->out($out);
		} else {
			switch ($command) {
				case 'send':
				$out .= "\tcake queue_email send \$id -batchSize \$size\n";
				$out .= "\t\tSends a batch of emails as defined by Configure::read('QueueEmail.batchSize').\n";
				$out .= "\t\tYou may also overwrite that size by passing a number to the switch -batchSize.\n";
				$out .= "\t\tIf \$id is passed, it will send that single email instead.\n";
				break;
				default:
				$out .= "$command does not exist. Run 'help' for a list of commands.\n";
				break;
			}

			$this->out($out);
		}
	}

/**
 * Sends a batch of emails or a single email
 *
 * ### Args
 * - 0: A specific email's id to send (leave empty to send a batch)
 *
 * ### Params
 * - `batchSize` Overrides default and configured batch size 
 */
	function send() {
		if (!empty($this->args[0])) {
			if ($this->_send($this->Queue->read(null, $this->args[0]))) {
				$this->out('Sent email #'.$this->args[0].' successfully!');
				$this->Queue->delete($this->args[0]);
			} else {
				$this->out('Error sending email #'.$this->args[0].'!');
			}
			$this->_stop();
			return;
		}

		$limit = $this->limit;
		if (Configure::read('QueueEmail.batchSize') !== null) {
			$limit = Configure::read('QueueEmail.batchSize');
		}
		if (isset($this->params['batchSize'])) {
			$limit = $this->params['batchSize'];
		}

		// get batch
		$emails = $this->Queue->find('all', array('limit' => $limit));

		foreach ($emails as $email) {
			if ($this->_send($email)) {
				$this->out('Sent email #'.$email['Queue']['id'].' successfully!');
				$this->Queue->delete($email['Queue']['id']);
			} else {
				$this->out('Error sending email #'.$email['Queue']['id'].'!');
			}
		}
	}

/**
 * Sends an email based on the results
 *
 * @param array $results
 * @return boolean Success
 * @access protected
 */
	function _send($results) {
		$this->_reset();
		$this->Controller->viewVars = @unserialize($results['Queue']['view_vars']);
		unset($results['Queue']['view_vars']);

		// attach non-null values to their EmailComponent variable counterpart
		foreach ($results['Queue'] as $var => $val) {
			if ($val !== null) {
				if (@unserialize($val) !== false) {
					$val = unserialize($val);
				}
				$varName = lcfirst(Inflector::camelize($var));
				if (in_array($varName, array('header', 'message'))) {
					$varName = '__'.$varName;
				}
				$this->Email->{$varName} = $val;
			}
		}
		return $this->Email->{'_'.$this->Email->delivery}();
	}

/**
 * Resets all EmailComponent properties
 *
 * @access protected
 */
	function _reset() {
		$this->Email->reset();
		$this->Email->delivery = 'mail';
		$this->Email->layout = 'default';
		$this->Email->sendAs = 'text';
	}
} 

?>