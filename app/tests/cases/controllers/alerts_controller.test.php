<?php
/* Alerts Test cases generated on: 2010-07-09 11:07:53 : 1278699053 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail'));
App::import('Controller', 'Alerts');

Mock::generate('QueueEmailComponent');
Mock::generatePartial('AlertsController', 'TestAlertsController', array('render', 'redirect', '_stop', 'header'));

class AlertsControllerTestCase extends CoreTestCase {
	var $fixtures = array('app.ministries_rev', 'app.involvements_rev','app.alert', 'app.group', 'app.user', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.roster', 'app.role', 'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member', 'app.household', 'app.publication', 'app.publications_user', 'app.alerts_user', 'app.log', 'app.app_setting', 'app.aco', 'app.aro', 'app.aros_aco');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		$this->loadFixtures('Aco', 'Aro', 'ArosAco');
		$this->loadFixtures('Alert', 'Group', 'AlertsUser', 'User');
		$this->Alerts =& new TestAlertsController();
		$this->Alerts->constructClasses();
		$this->Alerts->Component->initialize($this->Alerts);
		$this->Alerts->QueueEmail = new MockQueueEmailComponent();
		$this->Alerts->QueueEmail->setReturnValue('send', true);
		$this->Alerts->Session->write('Auth.User', array('id' => 1));
		$this->Alerts->Session->write('User', array('Group' => array('id' => 1)));
		$this->testController = $this->Alerts;
	}

	function endTest() {
		$this->Alerts->Session->destroy();
		unset($this->Alerts);		
		ClassRegistry::flush();
	}

	function testView() {
		$vars = $this->testAction('/alerts/view/1');
		$this->assertEqual($vars['alert'], array());

		$this->Alerts->Session->write('User', array('Group' => array('id' => 8)));
		$vars = $this->testAction('/alerts/view/1');
		$expected = array(
			'id' => 1,
			'name' => 'A User-level alert',
			'description' => 'Alert description 1',
			'created' => '2010-04-27 14:04:02',
			'modified' => '2010-06-02 12:27:38',
			'group_id' => 8,
			'importance' => 'medium',
			'expires' => NULL
		);
		$result = $vars['alert']['Alert'];
		$this->assertEqual($result, $expected);

		$this->Alerts->Session->write('User', array('Group' => array('id' => 8)));
		$vars = $this->testAction('/alerts/view/4');
		$this->assertEqual($vars['alert'], array());
	}

	function testHistory() {
		$this->Alerts->Session->write('Auth.User', array('id' => 1));
		$this->Alerts->Session->write('User', array('Group' => array('id' => 8)));
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
					'importance' => 'medium',
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
					'importance' => 'medium',
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
					'importance' => 'medium',
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
			'importance' => 'medium',
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
			'importance' => 'medium'
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