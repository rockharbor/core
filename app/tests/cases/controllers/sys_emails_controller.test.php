<?php
/* SysEmails Test cases generated on: 2010-08-05 08:08:46 : 1281021586 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail'));
App::import('Controller', 'SysEmails');

Mock::generate('QueueEmailComponent');
Mock::generatePartial('SysEmailsController', 'MockSysEmailsController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class SysEmailsControllerTestCase extends CoreTestCase {
	
	function startTest() {
		$this->loadFixtures('User', 'Group', 'Involvement', 'Roster');
		$this->SysEmails =& new MockSysEmailsController();
		$this->SysEmails->constructClasses();
		$this->SysEmails->QueueEmail = new MockQueueEmailComponent();
		$this->SysEmails->QueueEmail->setReturnValue('send', true);
		$this->testController = $this->SysEmails;
	}

	function endTest() {
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
		$vars = $this->testAction('/sys_emails/compose/test');
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
		$this->assertEqual($this->SysEmails->Session->read('Message.flash.element'), 'flash_success');
		$this->assertEqual($vars['message'], 'Test message');
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

		$vars = $this->testAction('/sys_emails/compose/'.$vars['cacheuid'], array(
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
		$this->assertEqual($this->SysEmails->Session->read('Message.flash.element'), 'flash_success');
	}

}
?>