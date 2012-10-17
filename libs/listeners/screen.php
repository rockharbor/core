<?php
/*
 * Imports
 */
App::import('Lib', 'Referee.RefereeListener');

/**
 * ScreenListener
 * 
 * Restores error handling in the browser when debug is enabled.
 *
 * @author Frank de Graaf (Phally)
 */
class ScreenListener extends RefereeListener {

/**
 * Triggered when we're passed an error from the WhistleComponent
 *
 * @param array $error
 * @param array $configuration
 * @return null
 * @access public
 */
	public function error($error = array()) {
		$error += array(
			'level' => null,
			'message' => null,
			'file' => null,
			'line' => null,
			'context' => null,
		);
		if (Configure::read() > 0) {
			call_user_func_array(array('Debugger', 'handleError'), array(
				$error['level'],
				$error['message'],
				$error['file'],
				$error['line'],
				$error['context']
			));
		}
	}
}
