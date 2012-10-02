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

/**
 * Force using limited layout for errors
 * 
 * @param string $template Template to render
 */
	function _outputMessage($template) {
		$this->controller->layout = 'ajax';
		parent::_outputMessage($template);
	}

/**
 * Error shown when a user tries to access a private item
 * 
 * @param array $params 
 */
	function privateItem($params = array()) {
		$this->controller->set('type', $params['type']);
		$this->_outputMessage('private_item');
	}

/**
 * Error shown when a user tries to perform an action with no items selected
 * or a missing search
 * 
 * @param array $params 
 */	
	function invalidMultiSelectSelection($params = array()) {
		$this->_outputMessage('invalid_multiselect_selection');
	}
	
}