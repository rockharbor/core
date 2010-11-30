<?php
/* SysEmails Test cases generated on: 2010-08-05 08:08:46 : 1281021586 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail.QueueEmail', 'Notifier'));
App::import('Controller', 'SysEmails');

Mock::generatePartial('QueueEmailComponent', 'MockQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('NotifierComponent', 'MockNotifierComponent', array('_render'));
Mock::generatePartial('SysEmailsController', 'MockSysEmailsController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class SysEmailsControllerTestCase extends CoreTestCase {
	
	function startTest() {
		$this->loadFixtures('User', 'Group', 'Involvement', 'Roster', 'Profile');
		$this->SysEmails =& new MockSysEmailsController();
		$this->SysEmails->__construct();
		$this->SysEmails->constructClasses();
		$this->SysEmails->Notifier = new MockNotifierComponent();
		$this->SysEmails->Notifier->initialize($this->Involvements);
		$this->SysEmails->Notifier->setReturnValue('_render', 'Notification body text');
		$this->SysEmails->Notifier->QueueEmail = new MockQueueEmailComponent();
		$this->SysEmails->Notifier->QueueEmail->initialize($this->SysEmails);
		$this->SysEmails->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->SysEmails->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->SysEmails->setReturnValue('isAuthorized', true);
		$this->loadSettings();
		$this->testController = $this->SysEmails;
	}

	function endTest() {
		$this->unloadSettings();
		$this->SysEmails->Session->destroy();
		unset($this->SysEmails);		
		ClassRegistry::flush();
	}

	function testComposeToUser() {
		$vars = $this->testAction('/sys_emails/compose/model:User/User:1');
		$this->assertPattern("/[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}/", $vars['cacheuid']);
		$this->assertEqual($vars['toUsers'][0]['User']['username'], 'jharris');

		$vars = $this->testAction('/sys_emails/compose/model:User/User:1,2');
		$results = Set::extract('/User/username', $vars['toUsers']);
		$expected = array(
			'jharris',
			'rickyrockharbor'
		);
		$this->assertEqual($results, $expected);

		$this->SysEmails->Session->write('MultiSelect.test', array(
			'selected' => array(
				1,2
			)
		));
		$vars = $this->testAction('/sys_emails/compose/test/model:User');
		$results = Set::extract('/User/username', $vars['toUsers']);
		$expected = array(
			'jharris',
			'rickyrockharbor'
		);
		$this->assertEqual($results, $expected);

		$data = array(
			'SysEmail' => array(
				'body' => 'Test message',
				'subject' => 'Email'
			)
		);
		$vars = $this->testAction('/sys_emails/compose/model:User/User:1', array(
			'data' => $data
		));
		$this->assertPattern('/1\/1/', $this->SysEmails->Session->read('Message.flash.message'));
		$this->assertEqual($this->SysEmails->Session->read('Message.flash.element'), 'flash'.DS.'success');
		$this->assertEqual($vars['content'], 'Test message');
	}

	function testComposeToRoster() {
		$vars = $this->testAction('/sys_emails/compose/model:Involvement/Involvement:1');
		$this->assertPattern("/[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}/", $vars['cacheuid']);

		$results = Set::extract('/User/username', $vars['toUsers']);
		$expected = array(
			'rickyrockharbor',
			'rickyrockharborjr'
		);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/sys_emails/compose/'.$vars['cacheuid'].'/model:User', array(
			'data' => array(
				'SysEmail' => array(
					'body' => 'Test message',
					'subject' => 'Email'
				)
			)
		));
		$results = Set::extract('/User/username', $vars['toUsers']);
		$expected = array(
			'rickyrockharbor',
			'rickyrockharborjr'
		);
		$this->assertEqual($results, $expected);
		$this->assertPattern('/2\/2/', $this->SysEmails->Session->read('Message.flash.message'));
		$this->assertEqual($this->SysEmails->Session->read('Message.flash.element'), 'flash'.DS.'success');
	}

}
?>