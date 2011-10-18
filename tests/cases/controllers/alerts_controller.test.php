<?php
/* Alerts Test cases generated on: 2010-07-09 11:07:53 : 1278699053 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('Notifier', 'QueueEmail.QueueEmail'));
App::import('Controller', 'Alerts');

Mock::generatePartial('QueueEmailComponent', 'MockAlertsQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('NotifierComponent', 'MockAlertsNotifierComponent', array('_render'));
Mock::generatePartial('AlertsController', 'TestAlertsController', array('render', 'redirect', '_stop', 'header'));

class AlertsControllerTestCase extends CoreTestCase {
	
	function startTest() {
		$this->loadFixtures('Alert', 'Group', 'AlertsUser', 'User');
		$this->Alerts =& new TestAlertsController();
		$this->Alerts->__construct();
		$this->Alerts->constructClasses();
		$this->Alerts->Notifier = new MockAlertsNotifierComponent();
		$this->Alerts->Notifier->initialize($this->Alerts);
		$this->Alerts->Notifier->setReturnValue('_render', 'Notification body text');
		$this->Alerts->Notifier->QueueEmail = new MockAlertsQueueEmailComponent();
		$this->Alerts->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Alerts->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->testController = $this->Alerts;
	}

	function endTest() {
		$this->Alerts->Session->destroy();
		unset($this->Alerts);		
		ClassRegistry::flush();
	}

	function testIndex() {
		$vars = $this->testAction('/alerts/index');
		$results = Set::extract('/Alert/read_by_users', $vars['alerts']);
		$this->assertEqual($results, array(1, 0, 0, 0));
	}

	function testView() {
		$this->su(array(
			'Group' => array('id' => 8)
		));
		$vars = $this->testAction('/alerts/view/1');
		$result = Set::extract('/Alert/id', $vars['alert']);
		$expected = array(1);
		$this->assertEqual($result, $expected);

		$this->su(array(
			'Group' => array('id' => 8)
		));
		$vars = $this->testAction('/alerts/view/4');
		$this->assertEqual($vars['alert'], array());

		$this->su(array(
			'Group' => array('id' => 1)
		));
		$vars = $this->testAction('/alerts/view/4');
		$result = Set::extract('/Alert/id', $vars['alert']);
		$expected = array(4);
		$this->assertEqual($result, $expected);
	}

	function testHistory() {
		$this->su(array(
			'Group' => array('id' => 8)
		));
		$vars = $this->testAction('/alerts/history', array(
			'return' => 'vars'
		));
		$expected = array(
			array(
				'Alert' => array(
					'id' => 1,
					'name' => 'A User-level alert',
					'description' => 'Alert description 1',
					'created' => '2010-04-27 14:04:02',
					'modified' => '2010-06-02 12:27:38',
					'group_id' => 8,
					'expires' => NULL
				)
			),
			array(
				'Alert' => array(
					'id' => 2,
					'name' => 'Another User-level alert',
					'description' => 'Alert description 2',
					'created' => '2010-04-27 14:04:02',
					'modified' => '2010-06-02 12:27:38',
					'group_id' => 8,
					'expires' => NULL
				)
			),
			array(
				'Alert' => array(
					'id' => 3,
					'name' => 'Yet Another User-level alert',
					'description' => 'Alert description 3',
					'created' => '2010-04-27 14:04:02',
					'modified' => '2010-06-02 12:27:38',
					'group_id' => 8,
					'expires' => NULL
				)
			)
		);
		$this->assertEqual(Set::extract('/Alert', $vars['alerts']), $expected);
		$this->assertEqual($vars['read'], array(1));
	}

	function testRead() {
		$vars = $this->testAction('/alerts/read/2');
		$read = $this->Alerts->Alert->AlertsUser->find('all', array(
			'conditions' => array(
				'user_id' => 1
			)
		));
		$read = Set::extract('/AlertsUser/id', $read);
		$this->assertEqual($read, array(1,2));
	}

	function testReadMultiSelect() {
		$this->Alerts->Session->write('MultiSelect.test', array(
			'selected' => array(2,3)
		));
		$vars = $this->testAction('/alerts/read/test');
		$read = $this->Alerts->Alert->AlertsUser->find('all', array(
			'conditions' => array(
				'user_id' => 1
			)
		));
		$read = Set::extract('/AlertsUser/id', $read);
		$this->assertEqual($read, array(1,2,3));
	}

	function testAdd() {
		$data = array(
			'name' => 'Super Admin alert',
			'description' => 'Average Joes can\'t read it',
			'group_id' => 1,
			'expires' => null
		);
		$vars = $this->testAction('/alerts/add', array(
			'data' => $data
		));
		$this->Alerts->Alert->id = 5;
		$this->assertEqual($this->Alerts->Alert->field('name'), 'Super Admin alert');
	}

	function testEdit() {
		$data = array(
			'id' => 1,
			'group_id' => 4,
		);
		$this->testAction('/alerts/edit/1', array(
			'data' => $data
		));
		$alert = $this->Alerts->Alert->read(null, 1);
		$this->assertEqual($alert['Alert']['group_id'], 4);
		$this->assertNotEqual($alert['Alert']['modified'], '2010-06-02 12:27:38');
	}

	function testDelete() {
		$this->testAction('/alerts/delete/1');
		$this->assertFalse($this->Alerts->Alert->read(null, 1));
	}
	
}
?>