<?php
/* SysEmails Test cases generated on: 2010-08-05 08:08:46 : 1281021586 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail.QueueEmail'));
App::import('Controller', 'SysEmails');

Mock::generatePartial('QueueEmailComponent', 'MockSysEmailsQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('SysEmailsController', 'MockSysEmailsController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class SysEmailsControllerTestCase extends CoreTestCase {
	
	function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('User', 'Group', 'Involvement', 'Roster', 'Profile');
		$this->SysEmails =& new MockSysEmailsController();
		$this->SysEmails->__construct();
		$this->SysEmails->constructClasses();
		$this->SysEmails->Notifier->QueueEmail = new MockSysEmailsQueueEmailComponent();
		$this->SysEmails->Notifier->QueueEmail->enabled = true;
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
	
	function testEmailLeader() {
		$this->loadFixtures('Leader');
		
		$vars = $this->testAction('/sys_emails/email_leader/4');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array(2));
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
				'subject' => 'Email',
				'email_users' => 'users'
			)
		);
		$vars = $this->testAction('/sys_emails/compose/model:User/User:1', array(
			'data' => $data
		));
		$this->assertEqual($this->SysEmails->Session->read('Message.flash.element'), 'flash'.DS.'success');
	}
	
	function testEmailHouseholdContact() {
		$this->loadFixtures('HouseholdMember', 'Household');
		
		$vars = $this->testAction('/sys_emails/compose/model:User/User:1', array(
			'data' => array(
				'SysEmail' => array(
					'subject' => 'email to household contacts only',
					'body' => 'Email!',
					'email_users' => 'household_contact'
				)
			)
		));
		
		$results = $vars['allToUsers'];
		$expected = array(1);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/sys_emails/compose/model:User/User:100', array(
			'data' => array(
				'SysEmail' => array(
					'subject' => 'email to household contacts only',
					'body' => 'Email!',
					'email_users' => 'household_contact'
				)
			)
		));
		
		$results = $vars['allToUsers'];
		$expected = array(1);
		$this->assertEqual($results, $expected);
		
		$this->SysEmails->Session->write('MultiSelect.test', array(
			'selected' => array(97, 98, 99, 100),
			'search' => array()
		));
		$vars = $this->testAction('/sys_emails/compose/test', array(
			'data' => array(
				'SysEmail' => array(
					'subject' => 'email to household contacts only',
					'body' => 'Email!',
					'email_users' => 'household_contact'
				)
			)
		));
		
		$results = $vars['allToUsers'];
		sort($results);
		$expected = array(1, 2, 3);
		$this->assertEqual($results, $expected);
		
		$this->SysEmails->Session->write('MultiSelect.test', array(
			'selected' => array(97, 98, 99, 100),
			'search' => array()
		));
		$vars = $this->testAction('/sys_emails/compose/test', array(
			'data' => array(
				'SysEmail' => array(
					'subject' => 'email to both',
					'body' => 'Email!',
					'email_users' => 'both'
				)
			)
		));
		
		$results = $vars['allToUsers'];
		sort($results);
		$expected = array(1, 2, 3, 97, 98, 99, 100);
		$this->assertEqual($results, $expected);
	}

}
?>