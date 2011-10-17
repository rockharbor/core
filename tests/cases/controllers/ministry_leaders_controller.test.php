<?php
/* Leaders Test cases generated on: 2010-07-14 12:07:47 : 1279136267 */
App::import('Lib', 'CoreTestCase');
App::import('Controller', array('MinistryLeaders'));

Mock::generatePartial('MinistryLeadersController', 'MockMinistryLeadersController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header'));

class MinistryLeadersControllerTestCase extends CoreTestCase {

	function startTest() {
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
		$results = Set::extract('/Leader/id', $vars['leaders']);
		sort($results);
		$this->assertEqual($results, array(4));
		
		$this->loadFixtures('Role');
		$vars = $this->testAction('ministry_leaders/dashboard/User:1');
		$results = Set::extract('/Ministry/Role/id', $vars['leaders']);
		sort($results);
		$this->assertEqual($results, array(1,2));
	}
}
?>