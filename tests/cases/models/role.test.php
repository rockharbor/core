<?php
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Role');

class RoleTestCase extends CoreTestCase {

	public function startTest($method) {
		parent::startTest($method);
		$this->Role =& ClassRegistry::init('Role');
	}

	public function endTest() {
		unset($this->Role);
		ClassRegistry::flush();
	}

	public function testGetRoles() {
		$this->loadFixtures('Role', 'Ministry');

		$results = $this->Role->findRoles();
		$expected = array();
		$this->assertEqual($results, $expected);

		$results = $this->Role->findRoles(3);
		$expected = array(3);
		$this->assertEqual($results, $expected);

		$results = $this->Role->findRoles(5);
		$expected = array(3);
		$this->assertEqual($results, $expected);

		$results = $this->Role->findRoles(4);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);

		$results = $this->Role->findRoles(6);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);
	}
}