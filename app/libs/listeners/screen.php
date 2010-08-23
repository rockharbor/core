<?php
/**
 * ScreenListener
 * 
 * Restores error handling in the browser when debug is enabled.
 *
 * @author Frank de Graaf (Phally)
 */
class ScreenListener {

/**
 * Triggered when we're passed an error from the WhistleComponent
 *
 * @param array $error
 * @param array $configuration
 * @return null
 * @access public
 */
	public function error($error, $configuration = array()) {
		if (Configure::read() > 0) {
			call_user_func_array(array('Debugger', 'handleError'), array_values($error));
		}
	}
}
?>