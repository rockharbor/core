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
 * The controller we're testing. Set to null to use the original
 * `CakeTestCase::testAction` function.
 * 
 * @var object
 */
	var $testController = null;

/**
 * Tests an action using the controller itself and skipping the dispatcher, and
 * returning the view vars.
 *
 * Since `CakeTestCase::testAction` was causing so many problems and is
 * incredibly slow, it is overwritten here to go about it a bit differently.
 * Import `CoreTestCase` from 'Lib' and extend test cases using `CoreTestCase`
 * instead to gain this functionality.
 *
 * For backwards compatibility with the original `CakeTestCase::testAction`, set
 * `testController` to `null`.
 *
 * ### Options:
 * - `data` Data to pass to the controller
 *
 * ### Limitations:
 * - only reinstantiates the default model
 *
 * @param string $url The url to test
 * @param array $options A list of options
 * @return array The view vars
 * @link http://mark-story.com/posts/view/testing-cakephp-controllers-the-hard-way
 */
	function testAction($url = '', $options = array()) {		
		if (is_null($this->testController)) {
			return parent::testAction($url, $options);
		}

		// reset parameters
		ClassRegistry::flush();		
		$this->testController->passedArgs = array();
		$this->testController->params = array();
		$this->testController->url = null;
		$this->testController->action = null;
		$this->testController->viewVars = array();
		$this->testController->{$this->testController->modelClass}->create();

		$default = array(
			'data' => array(),
			'method' => 'post'
		);
		$options = array_merge($default, $options);

		// set up the controller from the url
		$urlParams = Router::parse($url);
		if (strtolower($options['method']) == 'get') {
			$urlParams['url'] = array_merge($options['data'], $urlParams['url']);
		} else {
			$this->testController->data = $options['data'];
		}
		$this->testController->passedArgs = $urlParams['named'];
		$this->testController->params = $urlParams;
		$this->testController->url = $urlParams;		
		$this->testController->action = $urlParams['plugin'].'/'.$urlParams['controller'].'/'.$urlParams['action'];

		// go action!
		if (isset($this->testController->Auth)) {
			$this->testController->Auth->initialize($this->testController);
		}
		$this->testController->Component->startup($this->testController);
		$this->testController->beforeFilter();
		$pass = '"'.implode('", "', $urlParams['pass']).'"';
		$funcArgs = '('.$pass.')';
		if ($pass == '""') {
			$funcArgs = '()';
		}
		eval('$this->testController->'.$urlParams['action'].$funcArgs.';');
		$this->testController->Component->shutdown($this->testController);
		$this->testController->afterFilter();
		return $this->testController->viewVars;
	}

}

?>