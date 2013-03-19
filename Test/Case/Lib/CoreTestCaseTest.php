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
	}

	public function endTest() {
		unset($this->CoreTestCase);
		ClassRegistry::flush();
	}

	public function testLoadAndUnloadSettings() {
		$this->loadSettings();

		$result = Core::read('general.church_site_url');
		$expected = 'http://www.rockharbor.org';
		$this->assertEqual($result, $expected);

		$this->unloadSettings();

		$result = Core::read('general.church_site_url');
		$expected = null;
		$this->assertEqual($result, $expected);
	}

	public function testSingleLine() {
		$text = "Something \r\n\twith\t\ttabs \nand   extra spacing";
		$result = $this->singleLine($text);
		$expected = 'Something with tabs and extra spacing';
		$this->assertEqual($result, $expected);
	}

	public function testSu() {
		$result = $this->CoreTestCase->su();
		$this->assertTrue($result);

		$results = CakeSession::read('Auth');
		$this->assertEqual($results['User']['id'], 1);
		$this->assertEqual($results['User']['username'], 'testadmin');

		$results = CakeSession::read('User');
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

		$results = CakeSession::read('Auth');
		$this->assertEqual($results['User']['id'], 3);
		$results = CakeSession::read('User');
		$this->assertEqual($results['Profile']['name'], 'New User');

		$addToUser = array(
			'Group' => array(
				'id' => 10
			)
		);
		$result = $this->CoreTestCase->su($addToUser, false);
		$this->assertTrue($result);

		$results = CakeSession::read('Auth');
		$this->assertEqual($results['User']['id'], 3);
		$results = CakeSession::read('User');
		$this->assertEqual($results['Profile']['name'], 'New User');
		$this->assertEqual($results['Group']['id'], 10);
	}

}

