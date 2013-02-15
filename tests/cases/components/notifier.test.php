<?php
App::import('Lib', 'CoreTestCase');
App::import('Model', array('Notification', 'Invitation'));
App::import('Component', array('ProxyNotifier', 'QueueEmail.QueueEmail'));

Mock::generatePartial('ProxyNotifierComponent', 'MockNotifierNotifierComponent', array('_render'));
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
		$this->loadFixtures('Notification', 'User', 'Profile', 'SysEmail');
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

	function testEmailConfigStandard() {
		$config = new EmailConfig();
		$config->default = array(
			'transport' => 'Mail'
		);
		$config->debug = array(
			'transport' => 'Smtp'
		);
		$this->Notifier->Config = $config;

		Configure::write('debug', 0);
		$countBefore = $this->Notifier->QueueEmail->Model->find('count');
		$this->Notifier->QueueEmail->expectNever('__smtp');
		$this->Notifier->QueueEmail->setReturnValueAt(0, '__mail', true);
		$this->Controller->set('ministry', 1);
		$this->assertTrue($this->Notifier->notify(array(
			'to' => 1,
			'template' => 'ministries_edit',
			'queue' => false
		)), 'email');
		$countAfter = $this->Notifier->QueueEmail->Model->find('count');
		$this->assertEqual($countBefore, $countAfter);
		Configure::write('debug', 2);

		$countBefore = $this->Notifier->QueueEmail->Model->find('count');
		$this->assertTrue($this->Notifier->notify(array(
			'to' => 1,
			'template' => 'ministries_edit'
		)), 'email');
		$countAfter = $this->Notifier->QueueEmail->Model->find('count');
		$this->assertEqual($countAfter-$countBefore, 1);
	}

	function testEmailConfigSmtp() {
		$config = new EmailConfig();
		$config->debug = array(
			'transport' => 'Smtp',
			'host' => 'smtp.example.com'
		);
		$this->Notifier->Config = $config;

		$this->Notifier->QueueEmail->expectNever('__mail');
		$this->Notifier->QueueEmail->setReturnValueAt(0, '__smtp', true);
		$this->Controller->set('ministry', 1);
		$this->assertTrue($this->Notifier->notify(array(
			'to' => 1,
			'template' => 'ministries_edit'
		)), 'email');
		$countAfter = $this->Notifier->QueueEmail->Model->find('count');

		$expected = array(
			'host' => 'smtp.example.com',
			'port' => 25,
			'timeout' => 30
		);
		$results = $this->Notifier->QueueEmail->smtpOptions;
		$this->assertEqual($results, $expected);

		$config = new EmailConfig();
		$config->debug = array(
			'transport' => 'Smtp',
			'host' => 'smtp2.example.com',
			'timeout' => 42,
			'username' => 'test',
			'password' => 'pass',
			'some' => 'value'
		);
		$this->Notifier->Config = $config;

		$this->Notifier->QueueEmail->setReturnValueAt(1, '__smtp', true);
		$this->Controller->set('ministry', 1);
		$this->assertTrue($this->Notifier->notify(array(
			'to' => 1,
			'template' => 'ministries_edit'
		)), 'email');
		$countAfter = $this->Notifier->QueueEmail->Model->find('count');

		$expected = array(
			'host' => 'smtp2.example.com',
			'port' => 25,
			'timeout' => 42,
			'username' => 'test',
			'password' => 'pass'
		);
		$results = $this->Notifier->QueueEmail->smtpOptions;
		$this->assertEqual($results, $expected);
	}

	function testNoQueue() {
		$this->Notifier->QueueEmail->setReturnValue('__smtp', true);
		$this->Notifier->QueueEmail->expectOnce('__smtp');

		// sends now
		$countBefore = $this->Notifier->QueueEmail->Model->find('count');
		$this->Controller->set('ministry', 1);
		$this->assertTrue($this->Notifier->notify(array(
			'to' => 1,
			'template' => 'ministries_edit',
			'queue' => false
		)), 'email');
		$countAfter = $this->Notifier->QueueEmail->Model->find('count');
		$this->assertEqual($countBefore, $countAfter);

		// queues
		$countBefore = $this->Notifier->QueueEmail->Model->find('count');
		$this->assertTrue($this->Notifier->notify(array(
			'to' => 1,
			'template' => 'ministries_edit'
		)), 'email');
		$countAfter = $this->Notifier->QueueEmail->Model->find('count');
		$this->assertEqual($countAfter-$countBefore, 1);
	}

	function testNormalizeUser() {
		$results = $this->Notifier->_normalizeUser('email@example.com');
		$expected = array(
			'User' => array(
				'id' => 0
			),
			'Profile' => array(
				'first_name' => 'email@example.com',
				'last_name' => '',
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
				'first_name' => 'Jeremy',
				'last_name' => 'Harris',
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
				'first_name' => 'Jeremy',
				'last_name' => 'Harris',
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
				'first_name' => 'jeremy',
				'last_name' => '',
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
				'first_name' => 'CORE',
				'last_name' => '',
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
		$this->assertEqual($this->Notifier->Controller->viewVars['include_greeting'], true);

		$queue = $this->Notifier->QueueEmail->Model->read();
		$this->assertEqual($queue['Queue']['to_id'], 1);
		$this->assertEqual($queue['Queue']['from_id'], 0);
		$this->assertEqual($queue['Queue']['from'], $expected);
		$this->assertPattern('/\[core\]/', $queue['Queue']['subject']);

		$this->assertTrue($this->Notifier->_send($user, array('from' => 2)));
		$expected = 'ricky rockharbor <ricky@rockharbor.org>';
		$this->assertEqual($this->Notifier->QueueEmail->from, $expected);

		$queue = $this->Notifier->QueueEmail->Model->read();
		$this->assertEqual($queue['Queue']['to_id'], 1);
		$this->assertEqual($queue['Queue']['from_id'], 2);
		$this->assertEqual($queue['Queue']['from'], $expected);
		$this->assertPattern('/\[core user\]/', $queue['Queue']['subject']);

		$this->Notifier->_send($user, array(
			'from' => 2,
			'attachments' => array(
				'/path/to/file.txt'
			)
		));
		$expected = array('/path/to/file.txt');
		$this->assertEqual($this->Notifier->QueueEmail->attachments, $expected);

		$result = $this->Notifier->Controller->view;
		$expected = 'View';
		$this->assertEqual($result, $expected);

		$result = $this->Notifier->_send($user, array(
			'from' => 1,
			'body' => '<span class="wysiwyg-color-green">green</span>'
		));
		$this->assertTrue($result);

		// test that css classes were made inline
		$result = $this->Notifier->QueueEmail->Model->read(array('message'));
		$result = $result['Queue']['message'];
		$this->assertPattern('/style=\"color\s*:\s*green/', $result);
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
