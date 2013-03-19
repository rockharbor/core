<?php
/* Notifications Test cases generated on: 2010-07-09 10:07:32 : 1278696092 */
App::uses('CoreTestCase', 'Lib');
App::uses('QueueEmailComponent', 'QueueEmail.Controller/Component');
App::uses('NotificationsController', 'Controller');

Mock::generatePartial('QueueEmailComponent', 'MockNotificationsQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('NotificationsController', 'TestNotificationsController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class NotificationsControllerTestCase extends CoreTestCase {

	public function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Notification');
		$this->Notifications =& new TestNotificationsController();
		$this->Notifications->__construct();
		$this->Notifications->constructClasses();
		$this->Notifications->Notifier->QueueEmail = new MockNotificationsQueueEmailComponent();
		$this->Notifications->Notifier->QueueEmail->enabled = true;
		$this->Notifications->Notifier->QueueEmail->initialize($this->Notifications);
		$this->Notifications->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Notifications->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->Notifications->setReturnValue('isAuthorized', true);
		$this->testController = $this->Notifications;
	}

	public function endTest() {
		$this->Notifications->Session->destroy();
		unset($this->Notifications);
		ClassRegistry::flush();
	}

	public function testQuick() {
		$this->loadFixtures('Alert', 'AlertsUser', 'Invitation', 'InvitationsUser', 'Group');
		$vars = $this->testAction('/notifications/quick/User:1', array(
			'return' => 'vars'
		));
		$this->assertEqual(count($vars['notifications']), 2);
		$this->assertEqual(count($vars['invitations']), 2);
		$this->assertEqual(count($vars['alerts']), 3);
		$this->assertEqual($vars['new'], 7);
	}

	public function testIndex() {
		$vars = $this->testAction('/notifications/index/User:1', array(
			'return' => 'vars'
		));
		$expected = array(
			array(
				'Notification' => array(
					'id' => 1,
					'user_id' => 1,
					'created' => '2010-06-24 14:37:38',
					'modified' => '2010-06-24 14:37:38',
					'read' => 0,
					'body' => 'You have been invited somewhere.'
				)
			),
			array(
				'Notification' => array(
					'id' => 2,
					'user_id' => 1,
					'created' => '2010-06-04 10:24:49',
					'modified' => '2010-06-24 10:21:54',
					'read' => 0,
					'body' => 'Jeremy Harris is now managing the campus Fischer.'
				)
			)
		);
		$this->assertEqual($vars['notifications'], $expected);
	}

	public function testRead() {
		$vars = $this->testAction('/notifications/read/3');
		$this->Notifications->Notification->id = 3;
		$this->assertFalse($this->Notifications->Notification->field('read'));

		$this->su(array(
			'User' => array('id' => 2)
		));
		$vars = $this->testAction('/notifications/read/3');
		$this->Notifications->Notification->id = 3;
		$this->assertTrue($this->Notifications->Notification->field('read'));

		$vars = $this->testAction('/notifications/read/3/0');
		$this->Notifications->Notification->id = 3;
		$this->assertFalse($this->Notifications->Notification->field('read'));
	}

	public function testMultiSelectRead() {
		$this->Notifications->Session->write('MultiSelect.test', array(
			'selected' => array(1,2)
		));
		$vars = $this->testAction('/notifications/read/0/1/mstoken:test');
		$results = $this->Notifications->Notification->find('all', array(
			'conditions' => array(
				'user_id' => 1
			)
		));
		$results = Set::extract('/Notification/read', $results);
		$expected = array(1, 1);
		$this->assertEqual($results, $expected);
	}

	public function testDelete() {
		$vars = $this->testAction('/notifications/delete/3');
		$this->assertNotNull($this->Notifications->Notification->read(null, 3));

		$this->su(array(
			'User' => array('id' => 2)
		));
		$vars = $this->testAction('/notifications/delete/3');
		$this->Notifications->Notification->id = 3;
		$this->assertFalse($this->Notifications->Notification->read(null, 3));
	}

	public function testMultiSelectDelete() {
		$this->Notifications->Session->write('MultiSelect.test', array(
			'selected' => array(1,2,3)
		));
		$vars = $this->testAction('/notifications/delete/0/mstoken:test');
		$results = $this->Notifications->Notification->find('count');
		$this->assertEqual($results, 2);
	}

}
