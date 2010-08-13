<?php
/**
 * CoreTestCase test classes.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.tests.cases.libs
 */

/**
 * Includes
 */
App::import('Lib', 'CoreTestCase');
App::import('Controller', 'App');
App::import('Component', 'RequestHandler');
App::import('Model', 'App');

/**
 * Dummy app model
 *
 * @package       core
 * @subpackage    core.app.tests.cases.libs
 */
class Dummy extends AppModel {
	var $useTable = false;

	var $actsAs = array('Dumber');
}
/**
 * Dummy component
 *
 * @package       core
 * @subpackage    core.app.tests.cases.libs
 */
class DumbComponent extends Object {
	var $enabled = false;

	function initialize() {
		$this->enabled = true;
	}

	function beforeRender(&$Controller) {
		$Controller->set('component', 'dumb!');
	}
}
/**
 * Dummy behavior
 *
 * @package       core
 * @subpackage    core.app.tests.cases.libs
 */
class DumberBehavior extends ModelBehavior {
	function beforeSave(&$Model) {
		$Model->invalidate('no_db', 'There\'s no database!');
		return false;
	}
}
/**
 * Dummy controller
 *
 * @package       core
 * @subpackage    core.app.tests.cases.libs
 */
class DummiesController extends Controller {
	var $name = 'Dummies';

	var $components = array('Session', 'Dumb');

	function isAuthorized() {
		return true;
	}

	function dummy_action($var) {
		$this->set('var', $var);
		return true;
	}

	function set_passed_var() {
		if (isset($this->passedArgs['foo'])) {
			$this->set('foo', $this->passedArgs['foo']);
		}
	}

	function test_save() {
		if (!empty($this->data)) {
			$success = $this->Dummy->save($this->data);
			$this->set('saveSuccess', $success);
		}
	}

	function get_me() {
		$this->set('query', $this->params['url']['query']);
	}
}

Mock::generatePartial('DummiesController', 'MockDummiesController', array('header', 'render', 'redirect'));
Mock::generatePartial('RequestHandlerComponent', 'MockRequestHandlerComponent', array('_header'));

/**
 * CoreTestCase test case
 *
 * @package       core
 * @subpackage    core.app.tests.cases.libs
 */
class CoreTestCaseTestCase extends CakeTestCase {

	function startTest() {
		$this->CoreTestCase =& new CoreTestCase();
		$this->Dummies = new MockDummiesController();
		$this->Dummies->constructClasses();
		$this->Dummies->RequestHandler = new MockRequestHandlerComponent();
		$this->Dummies->Component->initialize($this->Dummies);
		$this->CoreTestCase->testController = $this->Dummies;
	}

	function endTest() {
		unset($this->CoreTestCase);
		unset($this->Dummies);
		ClassRegistry::flush();
	}

	function testTestActionVars() {
		$vars = $this->CoreTestCase->testAction('/dummies/dummy_action/3');
		$this->assertEqual($vars['var'], 3);

		$vars = $this->CoreTestCase->testAction('/dummies/set_passed_var/foo:bar', array(
			'return' => 'vars'
		));
		$this->assertEqual($vars['foo'], 'bar');
	}

	function testGetParams() {
		$vars = $this->CoreTestCase->testAction('/dummies/get_me', array(
			'method' => 'get',
			'data' => array(
				'query' => 'This is my query'
			)
		));
		$this->assertEqual($vars['query'], 'This is my query');
	}

	function testExtension() {
		Router::parseExtensions('csv');
		$vars = $this->CoreTestCase->testAction('/dummies/dummy_action/test.csv');
		$this->assertEqual($this->Dummies->params['url']['ext'], 'csv');
	}

	function testComponent() {
		$vars = $this->CoreTestCase->testAction('/dummies/dummy_action/test');
		$this->assertEqual($vars['component'], 'dumb!');

		$this->assertTrue($this->Dummies->Dumb->enabled);
	}

	function testBehavior() {
		$vars = $this->CoreTestCase->testAction('/dummies/test_save/test', array(
			'data' => array(
				'somedata' => 'test'
			)
		));
		$result = array(
			'no_db' => 'There\'s no database!'
		);
		$this->assertEqual($this->Dummies->Dummy->validationErrors, $result);

		$result = array(
			'somedata' => 'test'
		);
		$this->assertEqual($this->Dummies->data, $result);

		$this->assertFalse($vars['saveSuccess']);
	}

}

?>