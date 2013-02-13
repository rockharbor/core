<?php
/* Leaders Test cases generated on: 2010-07-14 12:07:47 : 1279136267 */
App::import('Lib', 'CoreTestCase');
App::import('Controller', array('MinistryLeaders'));

Mock::generatePartial('MinistryLeadersController', 'MockMinistryLeadersController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class MinistryLeadersControllerTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Leader', 'User', 'Ministry', 'Role');
		$this->Leaders =& new MockMinistryLeadersController;
		$this->Leaders->__construct();
		$this->Leaders->constructClasses();
		$this->testController = $this->Leaders;
	}

	function endTest() {
		unset($this->Leaders);
		ClassRegistry::flush();
	}

	function testDashboard() {
		$vars = $this->testAction('ministry_leaders/dashboard/User:2');
		$results = Set::extract('/Ministry/id', $vars['ministries']);
		sort($results);
		$this->assertEqual($results, array());

		$this->loadFixtures('Role');
		$vars = $this->testAction('ministry_leaders/dashboard/User:1');
		$results = Set::extract('/Role/id', $vars['ministries']);
		sort($results);
		$expected = array();
		$this->assertEqual($results, $expected);

		$results = Set::extract('/Ministry/id', $vars['ministries']);
		$expected = array();
		$this->assertEqual($results, $expected);

		$data = array(
			'Filter' => array(
				'inactive' => 1
			)
		);
		$vars = $this->testAction('ministry_leaders/dashboard/User:1', array(
			'data' => $data
		));

		$results = Set::extract('/Role/id', $vars['ministries']);
		sort($results);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);

		$results = Set::extract('/Ministry/id', $vars['ministries']);
		$expected = array(4);
		$this->assertEqual($results, $expected);

		$data = array(
			'Filter' => array(
				'inactive' => 1,
				'affiliated' => 1
			)
		);
		$vars = $this->testAction('ministry_leaders/dashboard/User:1', array(
			'data' => $data
		));

		$results = Set::extract('/Role/id', $vars['ministries']);
		sort($results);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);

		$results = Set::extract('/Ministry/id', $vars['ministries']);
		sort($results);
		$expected = array(4, 6);
		$this->assertEqual($results, $expected);

	}
}
