<?php
/* SysEmails Test cases generated on: 2010-08-05 08:08:46 : 1281021586 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail.QueueEmail', 'Notifier'));
App::import('Controller', 'SysEmails');

Mock::generatePartial('QueueEmailComponent', 'MockSysEmailsQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('NotifierComponent', 'MockSysEmailsNotifierComponent', array('_render'));
Mock::generatePartial('SysEmailsController', 'MockSysEmailsController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class SysEmailsControllerTestCase extends CoreTestCase {
	
	function startTest() {
		$this->loadFixtures('User', 'Group', 'Involvement', 'Roster', 'Profile');
		$this->SysEmails =& new MockSysEmailsController();
		$this->SysEmails->__construct();
		$this->SysEmails->constructClasses();
		$this->SysEmails->Notifier = new MockSysEmailsNotifierComponent();
		$this->SysEmails->Notifier->initialize($this->Involvements);
		$this->SysEmails->Notifier->setReturnValue('_render', 'Notification body text');
		$this->SysEmails->Notifier->QueueEmail = new MockSysEmailsQueueEmailComponent();
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
	
	function testComposeToUsersAndLeaders() {
		$this->loadFixtures('Leader');
		
		$vars = $this->testAction('/sys_emails/compose/model:Involvement/Involvement:1/submodel:Both');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array(1, 2, 3));
	}
	
	function testComposeToRosters() {
		$this->loadFixtures('Roster');
		
		$this->SysEmails->Session->write('MultiSelect.test', array(
			'selected' => array(3,4,5),
			'search' => array()
		));
		$vars = $this->testAction('/sys_emails/compose/test/model:Roster');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array(1, 2));
	}

	function testMassCompose() {
		$this->loadFixtures('Campus', 'Ministry', 'Leader');

		$vars = $this->testAction('/sys_emails/compose/model:Involvement/Involvement:1/submodel:Roster');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array(2, 3));

		$this->SysEmails->Session->write('MultiSelect.test', array(
			'selected' => array(1,2),
			'search' => array()
		));
		$vars = $this->testAction('/sys_emails/compose/test/model:Involvement/submodel:Roster');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array(1, 2, 3));

		$this->SysEmails->Session->write('MultiSelect.test', array(
			'selected' => array(1, 2),
			'search' => array()
		));
		$vars = $this->testAction('/sys_emails/compose/test/model:Campus/submodel:Leader');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array());

		$this->SysEmails->Session->write('MultiSelect.test', array(
			'selected' => array(1),
			'search' => array()
		));
		$vars = $this->testAction('/sys_emails/compose/test/model:Ministry/submodel:Roster');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array(5));

		$vars = $this->testAction('/sys_emails/compose/model:Ministry/Ministry:1');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array(5));

		$vars = $this->testAction('/sys_emails/compose/model:Campus/Campus:1');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array(1, 5));

		$this->SysEmails->Session->write('MultiSelect.test', array(
			'selected' => array(1, 2),
			'search' => array()
		));
		$vars = $this->testAction('/sys_emails/compose/test/model:Involvement/submodel:Manager');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array(1, 2));
	}

	function testComposeToUser() {
		$vars = $this->testAction('/sys_emails/compose/model:User/User:1');
		$this->assertEqual($vars['toUsers'][0]['User']['username'], 'jharris');

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
		$this->assertEqual($this->SysEmails->Session->read('Message.flash.element'), 'flash'.DS.'success');
	}

}
?>