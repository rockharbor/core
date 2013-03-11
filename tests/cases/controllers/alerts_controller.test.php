<?php
/* Alerts Test cases generated on: 2010-07-09 11:07:53 : 1278699053 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail.QueueEmail'));
App::import('Controller', 'Alerts');

Mock::generatePartial('QueueEmailComponent', 'MockAlertsQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('AlertsController', 'TestAlertsController', array('render', 'redirect', '_stop', 'header', 'disableCache', 'cakeError'));

class AlertsControllerTestCase extends CoreTestCase {

	public function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Alert', 'Group', 'AlertsUser', 'User');
		$this->Alerts =& new TestAlertsController();
		$this->Alerts->__construct();
		$this->Alerts->constructClasses();
		$this->Alerts->Notifier->QueueEmail = new MockAlertsQueueEmailComponent();
		$this->Alerts->Notifier->QueueEmail->enabled = true;
		$this->Alerts->Notifier->QueueEmail->initialize($this->Alerts);
		$this->Alerts->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Alerts->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->testController = $this->Alerts;
	}

	public function endTest() {
		$this->Alerts->Session->destroy();
		unset($this->Alerts);
		ClassRegistry::flush();
	}

	public function testIndex() {
		$vars = $this->testAction('/alerts/index');
		$results = Set::extract('/Alert/read_by_users', $vars['alerts']);
		$this->assertEqual($results, array(1, 0, 0, 0));
	}

	public function testView() {
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

	public function testHistory() {
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

	public function testRead() {
		$vars = $this->testAction('/alerts/read/2');
		$read = $this->Alerts->Alert->AlertsUser->find('all', array(
			'conditions' => array(
				'user_id' => 1
			)
		));
		$read = Set::extract('/AlertsUser/id', $read);
		$this->assertEqual($read, array(1,2));
	}

	public function testReadMultiSelect() {
		$this->Alerts->Session->write('MultiSelect.test', array(
			'selected' => array(2,3)
		));
		$vars = $this->testAction('/alerts/read/mstoken:test');
		$read = $this->Alerts->Alert->AlertsUser->find('all', array(
			'conditions' => array(
				'user_id' => 1
			)
		));
		$read = Set::extract('/AlertsUser/id', $read);
		$this->assertEqual($read, array(1,2,3));
	}

	public function testAdd() {
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

	public function testEdit() {
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

	public function testDelete() {
		$this->testAction('/alerts/delete/1');
		$this->assertFalse($this->Alerts->Alert->read(null, 1));
	}

}
