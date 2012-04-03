<?php
/* Leaders Test cases generated on: 2010-07-14 12:07:47 : 1279136267 */
App::import('Lib', 'CoreTestCase');
App::import('Controller', array('InvolvementLeaders'));

Mock::generatePartial('InvolvementLeadersController', 'MockInvolvementLeadersController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class InvolvementLeadersControllerTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Leader', 'User', 'Ministry', 'Role', 'Involvement', 'Date');
		$this->Leaders =& new MockInvolvementLeadersController;
		$this->Leaders->__construct();
		$this->Leaders->constructClasses();
		$this->testController = $this->Leaders;
		$this->Session = new CakeSession();
		$this->Session->destroy();
	}

	function endTest() {
		$this->Session->destroy();
		unset($this->Leaders);
		unset($this->Session);
		ClassRegistry::flush();
	}

	function testDashboard() {
		$vars = $this->testAction('involvement_leaders/dashboard/User:1');
		$results = Set::extract('/Involvement/id', $vars['involvements']);
		sort($results);
		$this->assertEqual($results, array());

		$vars = $this->testAction('involvement_leaders/dashboard/User:100');
		$results = Set::extract('/Involvement/id', $vars['involvements']);
		sort($results);
		$this->assertEqual($results, array());
		
		$vars = $this->testAction('/involvement_leaders/dashboard/User:1', array(
			'data' => array(
				'Filter' => array(
					'previous' => 1,
					'inactive' => 0,
					'private' => 0
				)
			)
		));
		$results = Set::extract('/Involvement/id', $vars['involvements']);
		sort($results);
		$this->assertEqual($results, array(1));
		
		$vars = $this->testAction('/involvement_leaders/dashboard/User:1', array(
			'data' => array(
				'Filter' => array(
					'previous' => 1,
					'inactive' => 1,
					'private' => 1
				)
			)
		));
		$results = Set::extract('/Involvement/id', $vars['involvements']);
		sort($results);
		$this->assertEqual($results, array(1, 3));
	}
}
?>