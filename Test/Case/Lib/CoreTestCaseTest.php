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
App::uses('CoreTestCase', 'Lib');
App::uses('RequestHandlerComponent', 'Controller/Component');
App::uses('AppModel', 'Model');
App::uses('AppController', 'Controller');
App::uses('View', 'View');

/**
 * Dummy app model
 *
 * @package       core
 * @subpackage    core.app.tests.cases.libs
 */
class Dummy extends AppModel {
	public $useTable = false;

	public $actsAs = array('Dumber');
}
/**
 * Dummy component
 *
 * @package       core
 * @subpackage    core.app.tests.cases.libs
 */
class DumbComponent extends Object {
	public $enabled = false;

	public function initialize() {
		$this->enabled = true;
	}

	public function beforeRender(&$Controller) {
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
	public function beforeSave(&$Model) {
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
class DummiesController extends AppController {
	public $name = 'Dummies';

	public $components = array('Session', 'Dumb');

	public function __mergeVars() {
		parent::__mergeVars();
		unset($this->components['DebugKit.Toolbar']);
	}

	public function dummy_action($var) {
		$this->set('var', $var);
		return true;
	}

	public function set_passed_var() {
		if (isset($this->passedArgs['foo'])) {
			$this->set('foo', $this->passedArgs['foo']);
		}
	}

	public function test_save() {
		if (!empty($this->data)) {
			$success = $this->Dummy->save($this->data);
			$this->set('saveSuccess', $success);
		}
	}

	public function get_me() {
		$this->set('query', $this->params['url']['query']);
	}

	public function disableCache() {
		return true;
	}
}

/**
 * Dummy reporter to ignore painting results
 */
class DummyReporter extends SimpleReporter {
	public function paintSkip() {
		return;
	}
}

/**
 * CoreTestCase test case
 *
 * @package       core
 * @subpackage    core.app.tests.cases.libs
 */
class CoreTestCaseTestCase extends CoreTestCase {

	public function startTest($method) {
		parent::startTest($method);
		$this->CoreTestCase =& new CoreTestCase();
		$this->Dummies = new DummiesController();
		$this->Dummies->constructClasses();
		$this->CoreTestCase->testController = $this->Dummies;
	}

	public function endTest() {
		unset($this->CoreTestCase);
		unset($this->Dummies);
		ClassRegistry::flush();
	}

	public function testGetTests() {
		$_reporter = $this->_reporter;
		$this->_reporter = new DummyReporter();

		$this->testMethods = array('testGetTests');
		$result = array_values($this->getTests());
		$expected = array(
			'start',
			'startCase',
			'testGetTests',
			'endCase',
			'end'
		);
		$this->assertEqual($result, $expected);

		unset($this->testMethods);
		$this->_reporter = $_reporter;
	}

	public function testSingleLine() {
		$text = "Something \r\n\twith\t\ttabs \nand   extra spacing";
		$result = $this->singleLine($text);
		$expected = 'Something with tabs and extra spacing';
		$this->assertEqual($result, $expected);
	}

	public function testTestActionVars() {
		$vars = $this->CoreTestCase->testAction('/dummies/dummy_action/3');
		$this->assertEqual($vars['var'], 3);

		$vars = $this->CoreTestCase->testAction('/dummies/set_passed_var/foo:bar', array(
			'return' => 'vars'
		));
		$this->assertEqual($vars['foo'], 'bar');
	}

	public function testGetParams() {
		$vars = $this->CoreTestCase->testAction('/dummies/get_me', array(
			'method' => 'get',
			'data' => array(
				'query' => 'This is my query'
			)
		));
		$this->assertEqual($vars['query'], 'This is my query');
	}

	public function testQueryStringParams() {
		$vars = $this->CoreTestCase->testAction('/dummies/dummy_action/pass?with=querystring');
		$this->assertEqual($this->Dummies->params['url']['with'], 'querystring');

		$vars = $this->CoreTestCase->testAction('/dummies/get_me?with=querystring', array(
			'method' => 'get',
			'data' => array(
				'query' => 'This is my query'
			)
		));
		$this->assertEqual($this->Dummies->params['url']['with'], 'querystring');
		$this->assertEqual($this->Dummies->params['url']['query'], 'This is my query');
	}

	public function testExtension() {
		Router::parseExtensions('csv');
		$vars = $this->CoreTestCase->testAction('/dummies/dummy_action/testVar.csv');
		$this->assertEqual($this->Dummies->params['url']['ext'], 'csv');
	}

	public function testComponent() {
		$vars = $this->CoreTestCase->testAction('/dummies/dummy_action/testVar');
		$this->assertEqual($vars['component'], 'dumb!');

		$this->assertTrue($this->Dummies->Dumb->enabled);
	}

	public function testBehavior() {
		$vars = $this->CoreTestCase->testAction('/dummies/test_save/testVar', array(
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

	public function testSu() {
		$result = $this->CoreTestCase->su();
		$this->assertTrue($result);

		$results = $this->CoreTestCase->testController->Session->read('Auth');
		$this->assertEqual($results['User']['id'], 1);
		$this->assertEqual($results['User']['username'], 'testadmin');

		$results = $this->CoreTestCase->testController->Session->read('User');
		$this->assertEqual($results['Group']['id'], 1);
		$this->assertEqual($results['Profile']['primary_email'], 'test@test.com');

		$this->CoreTestCase->testAction('/dummies/dummy_action/0');
		$results = $this->CoreTestCase->testController->activeUser;
		$this->assertEqual($results['Group']['id'], 1);
		$this->assertEqual($results['Profile']['primary_email'], 'test@test.com');

		$newUser = array(
			'User' => array(
				'id' => 3
			),
			'Profile' => array(
				'name' => 'New User'
			)
		);
		$result = $this->CoreTestCase->su($newUser);
		$this->assertTrue($result);

		$results = $this->CoreTestCase->testController->Session->read('Auth');
		$this->assertEqual($results['User']['id'], 3);
		$results = $this->CoreTestCase->testController->Session->read('User');
		$this->assertEqual($results['Profile']['name'], 'New User');

		$this->CoreTestCase->testAction('/dummies/dummy_action/2');
		$results = $this->CoreTestCase->testController->activeUser;
		$this->assertEqual($results['User']['id'], 3);
		$this->assertEqual($results['Profile']['name'], 'New User');

		$addToUser = array(
			'Group' => array(
				'id' => 10
			)
		);
		$result = $this->CoreTestCase->su($addToUser, false);
		$this->assertTrue($result);

		$results = $this->CoreTestCase->testController->Session->read('Auth');
		$this->assertEqual($results['User']['id'], 3);
		$results = $this->CoreTestCase->testController->Session->read('User');
		$this->assertEqual($results['Profile']['name'], 'New User');
		$this->assertEqual($results['Group']['id'], 10);

		$this->CoreTestCase->testAction('/dummies/dummy_action/1');
		$results = $this->CoreTestCase->testController->activeUser;
		$this->assertEqual($results['User']['id'], 3);
		$this->assertEqual($results['Profile']['name'], 'New User');
		$this->assertEqual($results['Group']['id'], 10);
	}

}

