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

	function testAction($url, $options = array()) {
		$this->SysEmails->users = array();
		return parent::testAction($url, $options);
	}
	
	function testBugCompose() {
		$this->loadSettings();
		$_SERVER['HTTP_USER_AGENT'] = 'cli';
		
		$this->su();
		$this->su(array(
			'User' => array(
				'active' => 1
			),
			'Profile' => array(
				'child' => 0
			)
		), false);
		
		$vars = $this->testAction('/sys_emails/bug_compose');
		$results = Set::extract('/Profile/primary_email', $vars['toUsers']);
		$expected = array(Core::read('development.debug_email'));
		$this->assertEqual($results, $expected);
		
		$this->assertIsA($this->SysEmails->data['SysEmail']['body'], 'string');
		$this->assertIsA($this->SysEmails->data['SysEmail']['subject'], 'string');
		
		$this->unloadSettings();
	}
	
	function testLeader() {
		$this->loadFixtures('Leader');
		
		$vars = $this->testAction('/sys_emails/leader/4');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array(2));
	}
	
	function testInvolvement() {
		$this->loadFixtures('Leader');
		
		$vars = $this->testAction('/sys_emails/involvement/both/Involvement:1');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array(1, 2, 3));
		
		$vars = $this->testAction('/sys_emails/involvement/users/Involvement:1');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array(2, 3));

		$this->SysEmails->Session->write('MultiSelect.test', array(
			'selected' => array(1,2),
			'search' => array()
		));
		$vars = $this->testAction('/sys_emails/involvement/users/mstoken:test');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array(1, 2, 3));
		
		// simulated expired search
		$this->SysEmails->Session->write('MultiSelect.test', array(
			'selected' => array(1,2),
			'search' => array(),
			'created' => strtotime('-1 day')
		));
		$vars = $this->testAction('/sys_emails/involvement/users/mstoken:test');
		$this->assertEqual($this->SysEmails->Session->read('Message.flash.element'), 'flash'.DS.'failure');
		$this->assertFalse(isset($vars['toUsers']));
	}
	
	function testMinistry() {
		$this->loadFixtures('Ministry');
		
		$this->SysEmails->Session->write('MultiSelect.test', array(
			'selected' => array(1),
			'search' => array()
		));
		$vars = $this->testAction('/sys_emails/ministry/users/mstoken:test');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array(5));

		$vars = $this->testAction('/sys_emails/ministry/users/Ministry:1');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array(5));
		
		// simulated expired search
		$this->SysEmails->Session->write('MultiSelect.test', array(
			'selected' => array(1),
			'search' => array(),
			'created' => strtotime('-1 day')
		));
		$vars = $this->testAction('/sys_emails/ministry/users/mstoken:test');
		$this->assertEqual($this->SysEmails->Session->read('Message.flash.element'), 'flash'.DS.'failure');
		$this->assertFalse(isset($vars['toUsers']));
	}
	
	function testRoster() {
		$this->SysEmails->Session->write('MultiSelect.test', array(
			'selected' => array(3,4,5),
			'search' => array()
		));
		$vars = $this->testAction('/sys_emails/roster/mstoken:test');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array(1, 2));
		
		$this->SysEmails->Session->write('MultiSelect.test', array(
			'selected' => array(),
			'search' => array(
				'fields' => array(
					'id'
				),
				'conditions' => array(
					'Roster.involvement_id' => 1
				),
				'contain' => array(
					'User'
				)
			)
		));
		$vars = $this->testAction('/sys_emails/roster/mstoken:test');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array(2, 3));
		
		// simulated expired search
		$this->SysEmails->Session->write('MultiSelect.test', array(
			'selected' => array(3,4,5),
			'search' => array(),
			'created' => strtotime('-1 day')
		));
		$vars = $this->testAction('/sys_emails/roster/mstoken:test');
		$this->assertEqual($this->SysEmails->Session->read('Message.flash.element'), 'flash'.DS.'failure');
		$this->assertFalse(isset($vars['toUsers']));
	}

	function testUser() {
		$vars = $this->testAction('/sys_emails/user/User:1');
		$this->assertEqual($vars['toUsers'][0]['User']['username'], 'jharris');

		$this->SysEmails->Session->write('MultiSelect.test', array(
			'selected' => array(
				1,2
			)
		));
		$vars = $this->testAction('/sys_emails/user/mstoken:test');
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
				'email_users' => 'users',
				'to' => 1
			)
		);
		$vars = $this->testAction('/sys_emails/user/User:1', array(
			'data' => $data
		));
		$this->assertEqual($this->SysEmails->Session->read('Message.flash.element'), 'flash'.DS.'success');
		
		// simulated expired search
		$this->SysEmails->Session->write('MultiSelect.test', array(
			'selected' => array(1,2),
			'search' => array(),
			'created' => strtotime('-1 day')
		));
		$vars = $this->testAction('/sys_emails/user/mstoken:test');
		$this->assertEqual($this->SysEmails->Session->read('Message.flash.element'), 'flash'.DS.'failure');
		$this->assertFalse(isset($vars['toUsers']));
	}
	
	function testEmailHouseholdContact() {
		$this->loadFixtures('HouseholdMember', 'Household');
		
		$vars = $this->testAction('/sys_emails/user/User:1', array(
			'data' => array(
				'SysEmail' => array(
					'subject' => 'email to household contacts only',
					'body' => 'Email!',
					'email_users' => 'household_contact',
					'to' => 1
				)
			)
		));
		
		$results = $vars['allToUsers'];
		$expected = array(1);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/sys_emails/user/User:100', array(
			'data' => array(
				'SysEmail' => array(
					'subject' => 'email to household contacts only',
					'body' => 'Email!',
					'email_users' => 'household_contact',
					'to' => 100
				)
			)
		));
		
		$results = $vars['allToUsers'];
		$expected = array(1);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/sys_emails/user', array(
			'data' => array(
				'SysEmail' => array(
					'subject' => 'email to household contacts only',
					'body' => 'Email!',
					'email_users' => 'household_contact',
					'to' => '97, 98, 99, 100'
				)
			)
		));
		
		$results = $vars['allToUsers'];
		sort($results);
		$expected = array(1, 2, 3);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/sys_emails/user', array(
			'data' => array(
				'SysEmail' => array(
					'subject' => 'email to both',
					'body' => 'Email!',
					'email_users' => 'both',
					'to' => '97, 98, 99, 100'
				)
			)
		));
		
		$results = $vars['allToUsers'];
		sort($results);
		$expected = array(1, 2, 3, 97, 98, 99, 100);
		$this->assertEqual($results, $expected);
	}
	
}
