<?php
/**
 * App error class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app
 */

/**
 * App Error
 *
 * @package       core
 * @subpackage    core.app
 */

class AppError extends ErrorHandler {
	
	function privateItem($params = array()) {
		$this->controller->set('type', $params['type']);
		$this->_outputMessage('private_item');
	}
	
}