<?php
/**
 * CoreTestCase class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.lib
 */

/**
 * Includes
 */
require_once APP.'config'.DS.'routes.php';

/**
 * CoreTestCase class
 *
 * Extends the functionality of CakeTestCase
 *
 * @package       core
 * @subpackage    core.lib
 */
class CoreTestCase extends CakeTestCase {

/**
 * Tests an action using the controller itself and skipping the dispatcher, and
 * returning the view vars.
 *
 * Since `CakeTestCase::testAction` was causing so many problems and is
 * incredibly slow, it is overwritten here to go about it a bit differently.
 * Import `CoreTestCase` from 'Lib' and extend test cases using `CoreTestCase`
 * instead to gain this functionality.
 *
 * ### Options:
 * - `data` Data to pass to the controller
 *
 * @param object $controller The controller we're testing
 * @param string $url The url to test
 * @param array $options A list of options
 * @return array The view vars
 * @link http://mark-story.com/posts/view/testing-cakephp-controllers-the-hard-way
 */
	function testAction(&$controller, $url = '', $options = array()) {		
		$default = array(
			'data' => array()
		);
		$options = array_merge($default, $options);

		// set up the controller from the url
		$urlParams = Router::parse($url);
		$controller->passedArgs = $urlParams['named'];
		$controller->params = $urlParams;
		$controller->url = $urlParams;
		$controller->data = $options['data'];
		$controller->action = $urlParams['plugin'].'/'.$urlParams['controller'].'/'.$urlParams['action'];

		// go action!
		$controller->Component->initialize($controller);
		$controller->beforeFilter();
		$controller->Component->startup($controller);
		$pass = '"'.implode('", "', $urlParams['pass']).'"';
		eval('$controller->'.$urlParams['action'].'('.$pass.');');
		$controller->afterFilter();		

		return $controller->viewVars;
	}

}

?>