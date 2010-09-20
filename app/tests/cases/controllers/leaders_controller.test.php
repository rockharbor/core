<?php
/* Leaders Test cases generated on: 2010-07-14 12:07:47 : 1279136267 */
App::import('Lib', 'CoreTestCase');
App::import('Component', 'QueueEmail');
App::import('Controller', 'InvolvementLeaders');

Mock::generate('QueueEmailComponent');
Mock::generatePartial('InvolvementLeadersController', 'TestLeadersController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class LeadersControllerTestCase extends CoreTestCase {

	function startTest() {
		$this->Leaders =& new TestLeadersController();
		$this->Leaders->__construct();
		$this->Leaders->constructClasses();
		// necessary fixtures
		$this->loadFixtures('Leader', 'User', 'Profile', 'Involvement', 'Notification', 'Group');
		$this->Leaders->Component->initialize($this->Leaders);
		$this->Leaders->QueueEmail = new MockQueueEmailComponent();
		$this->Leaders->setReturnValue('isAuthorized', true);
		$this->Leaders->QueueEmail->setReturnValue('send', true);
		$this->testController = $this->Leaders;
	}

	function endTest() {
		unset($this->Leaders);		
		ClassRegistry::flush();
	}

	function testIndex() {
		$vars = $this->testAction('involvement_leaders/index/Involvement:1', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Leader', $vars['leaders']);
		$expected = array(
			array(
				'Leader' => array(
					'id' => 2,
					'user_id' => 1,
					'model' => 'Involvement',
					'model_id' => 1,
					'created' => '2010-04-09 07:28:57',
					'modified' => '2010-04-09 07:28:57'
				)
			)
		);
		$this->assertEqual($results, $expected);
	}

	function testAdd() {
		$data = array(
			'Leader' => array(
				'user_id' => 2,
				'model' => 'Involvement',
				'model_id' => 1
			)
		);
		$vars = $this->testAction('/involvement_leaders/add/Involvement:1', array(
			'return' => 'vars',
			'data' => $data
		));
		$this->assertEqual($vars['type'], 'leading');
		$this->assertEqual($vars['itemType'], 'Involvement');
		$this->assertEqual($vars['itemName'], 'CORE 2.0 testing');
		$this->assertEqual($vars['name'], 'ricky rockharbor');

		$results = $this->Leaders->Leader->User->Notification->find('count');
		$this->assertEqual($results, 7);
	}

	function testDelete() {
		$vars = $this->testAction('/involvement_leaders/delete/Involvement:1/User:1');
		$results = $this->Leaders->Leader->User->Notification->find('count');
		$this->assertEqual($results, 6);
	}

}
?>