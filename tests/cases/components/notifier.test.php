<?php
App::import('Lib', 'CoreTestCase');
App::import('Model', array('Notification', 'Invitation'));
App::import('Component', array('Notifier', 'QueueEmail.QueueEmail'));

Mock::generatePartial('NotifierComponent', 'MockNotifierNotifierComponent', array('_render'));
Mock::generatePartial('QueueEmailComponent', 'MockQueueEmailComponent', array('__smtp', '__mail'));

class TestNotifierController extends Controller {
	
	public $activeUser = array(
		'Profile' => array(
			'name' => 'Example',
			'primary_email' => 'example@example.com'
		)
	);
	
}

class NotifierTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Notification', 'User', 'Profile', 'Queue');
		$this->loadSettings();
		$this->Notification = ClassRegistry::init('Notification');
		$this->Controller = new TestNotifierController();
		$this->Notifier = new MockNotifierNotifierComponent();
		$this->Notifier->setReturnValue('_render', 'A notification');
		$this->Notifier->initialize($this->Controller, array());		
		$this->Notifier->QueueEmail = new MockQueueEmailComponent();
		$this->Notifier->QueueEmail->initialize($this->Controller, array());
	}

	function endTest() {
		$this->unloadSettings();
		unset($this->Notifier);
		unset($this->Notification);
		unset($this->Controller);
		ClassRegistry::flush();
	}
	
	function testNoQueue() {
		$this->Notifier->QueueEmail->setReturnValue('__smtp', true);
		$this->Notifier->QueueEmail->expectOnce('__smtp');
		
		// sends now
		$this->Controller->set('ministry', 1);
		$this->assertTrue($this->Notifier->notify(array(
			'to' => 1,
			'template' => 'ministries_edit',
			'queue' => false
		)), 'email');
		// queues
		$this->assertTrue($this->Notifier->notify(array(
			'to' => 1,
			'template' => 'ministries_edit'
		)), 'email');
	}
	
	function testNormalizeUser() {
		$results = $this->Notifier->_normalizeUser('email@example.com');
		$expected = array(
			'User' => array(
				'id' => 0
			),
			'Profile' => array(
				'name' => 'email@example.com',
				'primary_email' => 'email@example.com'
			)
		);
		$this->assertEqual($results, $expected);
		
		$results = $this->Notifier->_normalizeUser(1);
		$expected = array(
			'User' => array(
				'id' => 1
			),
			'Profile' => array(
				'name' => 'Jeremy Harris',
				'primary_email' => 'jharris@rockharbor.org'
			)
		);
		$this->assertEqual($results, $expected);
		
		$results = $this->Notifier->_normalizeUser('1');
		$expected = array(
			'User' => array(
				'id' => 1
			),
			'Profile' => array(
				'name' => 'Jeremy Harris',
				'primary_email' => 'jharris@rockharbor.org'
			)
		);
		$this->assertEqual($results, $expected);
		
		$results = $this->Notifier->_normalizeUser(array(
			'Profile' => array(
				'name' => 'jeremy',
				'primary_email' => 'jeremy@42pixels.com'
			)
		));
		$expected = array(
			'User' => array(
				'id' => 0
			),
			'Profile' => array(
				'name' => 'jeremy',
				'primary_email' => 'jeremy@42pixels.com'
			)
		);
		$this->assertEqual($results, $expected);
		$results = $this->Notifier->_normalizeUser();
		$expected = array(
			'User' => array(
				'id' => 0
			),
			'Profile' => array(
				'name' => 'CORE',
				'primary_email' => 'core@rockharbor.org'
			)
		);
		$this->assertEqual($results, $expected);
		
		$results = $this->Notifier->_normalizeUser(array(
			'Profile' => array(
				'name' => 'jeremy'
			)
		));
		$this->assertNull($results);
	}
	
	function testInvite() {
		$this->loadFixtures('Invitation', 'InvitationsUser');
		
		$this->assertFalse($this->Notifier->invite(array(
			'to' => 1
		)));
		
		$countBefore = $this->Notifier->Invitation->find('count');
		$this->assertTrue($this->Notifier->invite(array(
			'to' => 1,
			'confirm' => '/confirmation/path',
			'deny' => '/confirmation/path',
			'template' => 'some_invite'
		)));
		$countAfter = $this->Notifier->Invitation->find('count');
		$this->assertEqual($countAfter-$countBefore, 1);
		
		$countBefore = $this->Notifier->Invitation->find('count');
		$countCcBefore = $this->Notifier->Invitation->InvitationsUser->find('count');
		$this->assertTrue($this->Notifier->invite(array(
			'to' => 1,
			'cc' => 2,
			'confirm' => '/confirmation/path',
			'deny' => '/confirmation/path',
			'template' => 'some_invite'
		)));
		$countAfter = $this->Notifier->Invitation->find('count');
		$countCcAfter = $this->Notifier->Invitation->InvitationsUser->find('count');
		$this->assertEqual($countAfter-$countBefore, 1);
		$this->assertEqual($countCcAfter-$countCcBefore, 1);
		
		$countBefore = $this->Notifier->Invitation->find('count');
		$countCcBefore = $this->Notifier->Invitation->InvitationsUser->find('count');
		$this->assertTrue($this->Notifier->invite(array(
			'to' => 1,
			'cc' => array(2, 3, 5),
			'confirm' => '/confirmation/path',
			'deny' => '/confirmation/path',
			'template' => 'some_invite'
		)));
		$countAfter = $this->Notifier->Invitation->find('count');
		$countCcAfter = $this->Notifier->Invitation->InvitationsUser->find('count');
		$this->assertEqual($countAfter-$countBefore, 1);
		$this->assertEqual($countCcAfter-$countCcBefore, 3);
		
		$countBefore = $this->Notifier->Invitation->find('count');
		$countCcBefore = $this->Notifier->Invitation->InvitationsUser->find('count');
		$this->Notifier->Invitation->delete($this->Notifier->Invitation->id);
		$countAfter = $this->Notifier->Invitation->find('count');
		$countCcAfter = $this->Notifier->Invitation->InvitationsUser->find('count');
		$this->assertEqual($countAfter-$countBefore, -1);
		$this->assertEqual($countCcAfter-$countCcBefore, -3);
		
	}

	function testNotify() {
		$this->Controller->set('ministry', 1);
		$this->assertTrue($this->Notifier->notify(array(
			'to' => 1,
			'template' => 'ministries_edit'
		)));
		$this->assertFalse($this->Notifier->notify(array(
			'to' => 100,
			'template' => 'ministries_edit'
		)));
		$this->assertFalse($this->Notifier->notify(array(
			'to' => 4,
			'template' => 'ministries_edit'
		)));
	}

	function testSend() {
		$this->Notification->User->contain(array('Profile'));
		$user = $this->Notification->User->read(null, 1);

		$this->assertTrue($this->Notifier->_send($user));
		$expected = 'CORE <core@rockharbor.org>';
		$this->assertEqual($this->Notifier->QueueEmail->from, $expected);
		$this->assertEqual($this->Notifier->Controller->viewVars['toUser'], $user);
		
		$queue = $this->Notifier->QueueEmail->Model->read();
		$this->assertEqual($queue['Queue']['to_id'], 1);
		$this->assertEqual($queue['Queue']['from_id'], 0);
		$this->assertEqual($queue['Queue']['from'], $expected);

		$this->assertTrue($this->Notifier->_send($user, array('from' => 2)));
		$expected = 'ricky rockharbor <ricky@rockharbor.org>';
		$this->assertEqual($this->Notifier->QueueEmail->from, $expected);
		
		$queue = $this->Notifier->QueueEmail->Model->read();
		$this->assertEqual($queue['Queue']['to_id'], 1);
		$this->assertEqual($queue['Queue']['from_id'], 2);
		$this->assertEqual($queue['Queue']['from'], $expected);

		$this->Notifier->_send($user, array(
			'from' => 2,
			'attachments' => array(
				'/path/to/file.txt'
			)
		));
		$expected = array('/path/to/file.txt');
		$this->assertEqual($this->Notifier->QueueEmail->attachments, $expected);
	}

	function testSave() {
		$this->Notification->User->contain(array('Profile'));
		$user = $this->Notification->User->read(null, 1);

		$data = array(
			'template' => 'leaders_add'
		);
		$this->Notifier->_save($user, $data);
		$this->Notification->recursive = -1;
		$result = $this->Notification->read(array('user_id', 'read'));
		$expected = array(
			'Notification' => array(
				'user_id' => 1,
				'read' => false
			)
		);
		$this->assertEqual($result, $expected);
		
		$data = array(
			'template' => 'leaders_add',
			'type' => 'invitation'
		);
		$this->Notifier->_save($user, $data);
		$this->Notification->recursive = -1;
		$result = $this->Notification->read(array('user_id', 'read'));
		$expected = array(
			'Notification' => array(
				'user_id' => 1,
				'read' => false
			)
		);
		$this->assertEqual($result, $expected);
	}
}
?>