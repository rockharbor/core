<?php
/* SysEmails Test cases generated on: 2010-08-05 08:08:46 : 1281021586 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail.QueueEmail'));
App::import('Controller', 'SysEmails');

Mock::generatePartial('QueueEmailComponent', 'MockSysEmailsQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('SysEmailsController', 'MockSysEmailsController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class SysEmailsControllerTestCase extends CoreTestCase {

	public function startTest($method) {
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

	public function endTest() {
		$this->unloadSettings();
		$this->SysEmails->Session->destroy();
		unset($this->SysEmails);
		ClassRegistry::flush();
	}

	public function testAction($url, $options = array()) {
		$this->SysEmails->users = array();
		return parent::testAction($url, $options);
	}

	public function testView() {
		$this->loadFixtures('SysEmail', 'User', 'Profile');

		// not user 2's email
		$vars = $this->testAction('/sys_emails/view/4/User:2');
		$result = $vars['email'];
		$expected = array();
		$this->assertEqual($result, $expected);

		// sent from user 2
		$vars = $this->testAction('/sys_emails/view/2/User:2');
		$result = $vars['email']['SysEmail']['id'];
		$expected = 2;
		$this->assertEqual($result, $expected);

		// sent to user 2
		$vars = $this->testAction('/sys_emails/view/3/User:2');
		$result = $vars['email']['SysEmail']['id'];
		$expected = 3;
		$this->assertEqual($result, $expected);

		$result = $vars['email']['SysEmail']['message'];
		$this->assertIsA($result, 'Array');

		$result = $vars['email']['FromUser'];
		$this->assertIsA($result, 'Array');

		$result = $vars['email']['FromUser']['Profile']['name'];
		$expected = 'Jeremy Harris';
		$this->assertEqual($result, $expected);
	}

	public function testHtmlEmail() {
		$this->loadFixtures('SysEmail');

		$vars = $this->testAction('/sys_emails/html_email/3/User:2');

		$result = $vars['email']['SysEmail']['message'];
		$this->assertIsA($result, 'Array');

		$this->assertFalse($this->testController->layout);
	}

	public function testIndex() {
		$this->loadFixtures('SysEmail');

		$vars = $this->testAction('/sys_emails/index/User:2');
		$result = count($vars['emails']);
		$expected = 2;
		$this->assertEqual($result, $expected);

		$result = $vars['emails'][0][0]['message_count'];
		$expected = 2;
		$this->assertEqual($result, $expected);

		$result = $vars['emails'][1][0]['message_count'];
		$expected = 1;
		$this->assertEqual($result, $expected);

		$vars = $this->testAction('/sys_emails/index/User:2', array(
			'data' => array(
				'Filter' => array(
					'show' => 'both',
					'hide_system' => 0
				)
			)
		));
		$result = count($vars['emails']);
		$expected = 4;
		$this->assertEqual($result, $expected);

		$result = $this->testController->params['paging']['SysEmail']['count'];
		$expected = 4;
		$this->assertEqual($result, $expected);

		$vars = $this->testAction('/sys_emails/index/User:2', array(
			'data' => array(
				'Filter' => array(
					'show' => 'to',
					'hide_system' => 0
				)
			)
		));
		$result = count($vars['emails']);
		$expected = 3;
		$this->assertEqual($result, $expected);

		$vars = $this->testAction('/sys_emails/index/User:2', array(
			'data' => array(
				'Filter' => array(
					'show' => 'both',
					'hide_system' => 1
				)
			)
		));
		$result = count($vars['emails']);
		$expected = 3;
		$this->assertEqual($result, $expected);
	}

	public function testComposeValidation() {
		$vars = $this->testAction('/sys_emails/user/User:1');
		$results = $this->SysEmails->SysEmail->validationErrors;
		$this->assertTrue(empty($results));

		$vars = $this->testAction('/sys_emails/user/User:1', array(
			'data' => array(
				'SysEmail' => array(
					'to' => '1',
					'subject' => '',
					'body' => 'Email!',
					'email_users' => 'users',
					'include_signoff' => true,
					'include_greeting' => true
				)
			)
		));
		$this->assertTrue(array_key_exists('subject', $this->SysEmails->SysEmail->validationErrors));
		$results = Set::extract('/User/id', $vars['toUsers']);
		$expected = array(1);
		$this->assertEqual($results, $expected);
		$results = $this->SysEmails->Session->read('Message.flash.element');
		$expected = 'flash'.DS.'failure';
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/sys_emails/user/User:1', array(
			'data' => array(
				'SysEmail' => array(
					'subject' => 'This will send',
					'body' => 'Email!',
					'email_users' => 'users',
					'include_signoff' => true,
					'include_greeting' => false
				)
			)
		));
		$results = $this->SysEmails->SysEmail->validationErrors;
		$this->assertTrue(empty($results));
		$results = $this->SysEmails->Session->read('Message.flash.element');
		$expected = 'flash'.DS.'success';
		$this->assertEqual($results, $expected);
		// ensure it doesn't get overwritten by the notifier's defaults
		$results = $this->SysEmails->viewVars['include_greeting'];
		$expected = false;
		$this->assertEqual($results, $expected);
	}

	public function testLeader() {
		$this->loadFixtures('Leader');

		$vars = $this->testAction('/sys_emails/leader/4');
		$results = Set::extract('/User/id', $vars['toUsers']);
		sort($results);
		$this->assertEqual($results, array(2));
	}

	public function testInvolvement() {
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

	public function testMinistry() {
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

	public function testRoster() {
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
			),
			'all' => true
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

	public function testUser() {
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
				'to' => 1,
				'include_signoff' => true,
				'include_greeting' => true
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

		$this->SysEmails->Session->write('MultiSelect.test', array(
			'selected' => array(),
			'search' => array(
				'conditions' => array(
					'User.id' => 1
				)
			),
			'all' => true,
			'created' => time()
		));
		$vars = $this->testAction('/sys_emails/user/mstoken:test');
		$results = $vars['toUserIds'];
		$expected = array(1);
		$this->assertEqual($results, $expected);
	}

	public function testEmailHouseholdContact() {
		$this->loadFixtures('HouseholdMember', 'Household');

		$vars = $this->testAction('/sys_emails/user/User:1', array(
			'data' => array(
				'SysEmail' => array(
					'subject' => 'email to household contacts only',
					'body' => 'Email!',
					'email_users' => 'household_contact',
					'to' => 1,
					'include_signoff' => true,
					'include_greeting' => true
				)
			)
		));

		$results = $vars['toUserIds'];
		$expected = array(1);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/sys_emails/user/User:100', array(
			'data' => array(
				'SysEmail' => array(
					'subject' => 'email to household contacts only',
					'body' => 'Email!',
					'email_users' => 'household_contact',
					'to' => 100,
					'include_signoff' => true,
					'include_greeting' => true
				)
			)
		));

		$results = $vars['toUserIds'];
		$expected = array(1);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/sys_emails/user', array(
			'data' => array(
				'SysEmail' => array(
					'subject' => 'email to household contacts only',
					'body' => 'Email!',
					'email_users' => 'household_contact',
					'to' => '97, 98, 99, 100',
					'include_signoff' => true,
					'include_greeting' => true
				)
			)
		));

		$results = $vars['toUserIds'];
		sort($results);
		$expected = array(1, 2, 3);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/sys_emails/user', array(
			'data' => array(
				'SysEmail' => array(
					'subject' => 'email to both',
					'body' => 'Email!',
					'email_users' => 'both',
					'to' => '97, 98, 99, 100',
					'include_signoff' => true,
					'include_greeting' => true
				)
			)
		));

		$results = $vars['toUserIds'];
		sort($results);
		$expected = array(1, 2, 3, 97, 98, 99, 100);
		$this->assertEqual($results, $expected);
	}

}
