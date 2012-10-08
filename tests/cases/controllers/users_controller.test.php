<?php
/* Users Test cases generated on: 2010-08-11 07:08:43 : 1281537883 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail.QueueEmail', 'Cookie'));
App::import('Controller', 'Users');

Mock::generatePartial('QueueEmailComponent', 'MockUsersQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('CookieComponent', 'MockUsersCookieComponent', array('read', 'write', 'delete'));
Mock::generatePartial('UsersController', 'MockUsersController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

if (!defined('FULL_BASE_URL')) {
	define('FULL_BASE_URL', 'http://www.example.com');
}

class UsersControllerTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('User', 'Profile', 'Group');
		$this->loadSettings();
		$this->Users =& new MockUsersController();
		$this->Users->__construct();
		$this->Users->constructClasses();
		$this->Users->Cookie = new MockUsersCookieComponent();
		$this->Users->Cookie->initialize($this->Users, array());
		$this->Users->Cookie->enabled = true;
		$this->Users->Cookie->setReturnValue('write', true);
		$this->Users->Cookie->setReturnValue('delete', true);
		$this->Users->Notifier->QueueEmail = new MockUsersQueueEmailComponent();
		$this->Users->Notifier->QueueEmail->enabled = true;
		$this->Users->Notifier->QueueEmail->initialize($this->Users);
		$this->Users->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Users->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->Users->FilterPagination->initialize($this->Users);
		$this->testController = $this->Users;
	}

	function endTest() {
		$this->Users->Session->destroy();
		unset($this->Users);
		$this->unloadSettings();
		ClassRegistry::flush();
	}
	
	function testDelete() {
		$this->loadFixtures('Roster', 'Payment');
		
		$this->Users->expectAt(0, 'cakeError', array('invalidMultiSelectSelection'));
		$vars = $this->testAction('/users/delete');
		
		$vars = $this->testAction('/users/delete/1');
		$result = $this->Users->Session->read('Message.flash.element');
		$expected = 'flash'.DS.'success';
		
		$result = $this->Users->User->read(null, 1);
		$expected = false;
		$this->assertEqual($result, $expected);
		
		$result = $this->Users->User->Roster->read(null, 3);
		$expected = false;
		$this->assertEqual($result, $expected);
		
		$result = $this->Users->User->Payment->read(null, 1);
		$this->assertIsA($result, 'Array');
		
		$this->Users->Session->write('MultiSelect.test', array(
			'selected' => array(2, 3)
		));

		$vars = $this->testAction('/users/delete/0/mstoken:test');
		$result = $this->Users->Session->read('Message.flash.element');
		$expected = 'flash'.DS.'success';
		
		$result = $this->Users->User->read(null, 2);
		$expected = false;
		$this->assertEqual($result, $expected);
		
		$result = $this->Users->User->read(null, 3);
		$expected = false;
		$this->assertEqual($result, $expected);
	}
	
	function testRedirectOnResetPassword() {
		// trick CoreTestCase into not setting up a user
		$this->Users->Session->write('User', array());
		
		$this->Users->User->id = 1;
		$this->Users->User->saveField('reset_password', true);
		
		$vars = $this->testAction('/users/login', array(
			'data' => array(
				'User' => array(
					'username' => 'jharris',
					'password' => 'password',
					'remember_me' => false
				)
			)
		));
		$result = $this->Users->Session->read('Auth.User.id');
		$this->assertEqual($result, 1);
		$this->assertPattern('/reset/', $this->Users->Session->read('Message.flash.message'));
	}

	function testBoth() {
		$data = array(
			'User' => array(
				'id' => 1,
				'reset' => 'both',
				'username' => 'newusername',
				'password' => 'newpassword',
				'current_password' => 'password',
				'confirm_password' => 'newpassword'
			)
		);
		$vars = $this->testAction('/users/edit/User:1', array(
			'data' => $data
		));
		$user = $this->Users->User->read(null, 1);
		$result = $user['User']['password'];
		$expected = $this->Users->Auth->password('newpassword');
		$this->assertEqual($result, $expected);

		$result = $user['User']['username'];
		$this->assertEqual($result, 'newusername');
		
		$result = $user['User']['reset_password'];
		$this->assertFalse($result);

		$result = $vars['password'];
		$this->assertEqual($result, 'newpassword');

		$result = $this->Users->Session->read('Message.flash.element');
		$this->assertEqual($result, 'flash'.DS.'success');
	}

	function testEditUsername() {
		$data = array(
			'User' => array(
				'id' => 1,
				'reset' => 'username',
				'username' => 'newusername'
			)
		);
		$vars = $this->testAction('/users/edit/User:1', array(
			'data' => $data
		));
		$user = $this->Users->User->read(null, 1);
		$result = $user['User']['username'];
		$this->assertEqual($result, 'newusername');

		$result = $this->Users->Session->read('Message.flash.element');
		$this->assertEqual($result, 'flash'.DS.'success');
	}

	function testEditPassword() {
		$data = array(
			'User' => array(
				'id' => 1,
				'reset' => 'password',
				'password' => 'newpassword',
				'confirm_password' => 'not confirmed!',
				'current_password' => 'password'
			)
		);
		$vars = $this->testAction('/users/edit/User:1', array(
			'data' => $data
		));
		$this->assertTrue(!empty($this->Users->User->validationErrors));
		
		$user = $this->Users->User->read(null, 1);
		$result = $user['User']['password'];
		$expected = $this->Users->Auth->password('password');
		$this->assertEqual($result, $expected);
		
		$result = $user['User']['reset_password'];
		$this->assertFalse($result);

		$result = $this->Users->Session->read('Message.flash.element');
		$this->assertEqual($result, 'flash'.DS.'failure');

		$data = array(
			'User' => array(
				'id' => 1,
				'reset' => 'password',
				'password' => 'newpassword',
				'confirm_password' => 'newpassword',
				'current_password' => 'password'
			)
		);
		$vars = $this->testAction('/users/edit/User:1', array(
			'data' => $data
		));
		$user = $this->Users->User->read(null, 1);
		$result = $user['User']['password'];
		$expected = $this->Users->Auth->password('newpassword');
		$this->assertEqual($result, $expected);
		
		$result = $user['User']['reset_password'];
		$this->assertFalse($result);

		$result = $vars['password'];
		$this->assertEqual($result, 'newpassword');

		$result = $this->Users->Session->read('Message.flash.element');
		$this->assertEqual($result, 'flash'.DS.'success');
		
		$data = array(
			'User' => array(
				'id' => 2,
				'reset' => 'password',
				'password' => 'newpassword',
				'confirm_password' => 'newpassword',
				'current_password' => 'wrongpassword'
			)
		);
		$vars = $this->testAction('/users/edit/User:2', array(
			'data' => $data
		));
		$this->assertTrue(empty($this->Users->User->validationErrors));
		
		$user = $this->Users->User->read(null, 2);
		$result = $user['User']['password'];
		$expected = $this->Users->Auth->password('newpassword');
		$this->assertEqual($result, $expected);
		
		$result = $user['User']['reset_password'];
		$this->assertTrue($result);

		$result = $vars['password'];
		$this->assertEqual($result, 'newpassword');

		$result = $this->Users->Session->read('Message.flash.element');
		$this->assertEqual($result, 'flash'.DS.'success');
	}

	function testRestrictLogin() {
		// trick CoreTestCase into not setting up a user
		$this->Users->Session->write('User', array());
		$this->Users->Cookie->setReturnValue('read', null);
		
		// children can't log in
		$vars = $this->testAction('/users/login', array(
			'data' => array(
				'User' => array(
					'username' => 'rickyrockharbor',
					'password' => 'password',
					'remember_me' => true
				)
			)
		));
		$result = $this->Users->Session->read('Auth.User.id');
		$this->assertNull($result);
		
		// inactive users can't log in
		$vars = $this->testAction('/users/login', array(
			'data' => array(
				'User' => array(
					'username' => 'joe',
					'password' => 'password',
					'remember_me' => true
				)
			)
		));
		$result = $this->Users->Session->read('Auth.User.id');
		$this->assertNull($result);
		
		$this->Users->Session->destroy();
	}

	function testLogin() {
		$lastLoggedIn = $this->Users->User->read('last_logged_in', 1);

		// trick CoreTestCase into not setting up a user
		$this->Users->Session->write('User', array());
		
		$this->Users->Cookie->setReturnValueAt(0, 'read', null);
		$vars = $this->testAction('/users/login', array(
			'data' => array(
				'User' => array(
					'username' => 'jharris',
					'password' => 'password',
					'remember_me' => true
				)
			)
		));
		$result = $this->Users->Session->read('Auth.User.id');
		$this->assertEqual($result, 1);

		$result = $this->Users->Session->read('User.Profile.name');
		$this->assertEqual($result, 'Jeremy Harris');

		$result = $this->Users->Session->read('User.User.last_logged_in');
		$this->assertEqual($result, $lastLoggedIn['User']['last_logged_in']);
		
		$result = $this->Users->User->read(null, 1);
		$this->assertNotEqual($result['User']['last_logged_in'], $lastLoggedIn['User']['last_logged_in']);

		// logout and try with cookie
		$this->Users->Session->destroy();
		$this->Users->Session->write('User', array());
		$this->Users->Cookie->setReturnValueAt(1, 'read', array(
			'username' => 'jharris',
			'password' => '005b8f6046bb2039063d9dde0678f9f28ae38827'
		));
		$vars = $this->testAction('/users/login');
		$result = $this->Users->Session->read('Auth.User.id');
		$this->assertEqual($result, 1);
		$this->assertNull($this->Users->Session->read('Message.auth'));
		
		// logout fail with cookie (because of password change)
		$this->Users->Session->destroy();
		$this->Users->Session->write('User', array());
		
		$this->Users->Cookie->setReturnValueAt(2, 'read', array(
			'username' => 'no',
			'password' => 'access'
		));
		$vars = $this->testAction('/users/login');
		$this->assertNull($this->Users->Session->read('Auth'));
		$this->assertTrue(!empty($this->Users->User->validationErrors));
		
		$vars = $this->testAction('/users/dashboard');
		$this->assertNotNull($this->Users->Session->read('Message.auth'));
		
		$this->Users->Session->destroy();
	}

	function testForgotPassword() {
		$oldPassword = $this->Users->User->read('password', 1);
		$data = array(
			'User' => array(
				'forgotten' => 'jharris@rockharbor.org'
			)
		);
		$vars = $this->testAction('/users/forgot_password', array(
			'data' => $data
		));
		$result = $vars['password'];
		$this->assertNotEqual($oldPassword['User']['password'], $result);
		$result = $vars['username'];
		$this->assertEqual($result, 'jharris');
		$this->assertEqual($this->Users->Session->read('Message.flash.element'), 'flash'.DS.'success');
		$user = $this->Users->User->read('reset_password', 1);
		$this->assertTrue($user['User']['reset_password']);
		
		// test as if we just came from choose_user
		$oldPassword = $this->Users->User->read('password', 2);
		$vars = $this->testAction('/users/forgot_password/1');
		$result = $vars['password'];
		$this->assertNotEqual($oldPassword['User']['password'], $result);
		$this->assertEqual($this->Users->Session->read('Message.flash.element'), 'flash'.DS.'success');
	}

	function testRequestActivation() {
		$this->loadFixtures('MergeRequest', 'Address', 'Household', 'HouseholdMember');
		$MergeRequest = ClassRegistry::init('MergeRequest');

		$oldCount = $this->Users->User->find('count');

		$data = array(
			'User' => array(
				'username' => 'newusername'
			),
			'Address' => array(
				0 => array(
					'address_line_1' => '123 Main',
					'city' => 'Anytown',
					'state' => 'CA',
					'zip' => '12345'
				)
			),
			'Profile' => array(
				'primary_email' => 'test@test.com'
			)
		);
		$vars = $this->testAction('/users/request_activation/1', array(
			'data' => $data
		));
		$this->assertEqual($this->Users->Session->read('Message.flash.element'), 'flash'.DS.'success');
		$request = $MergeRequest->find('first', array(
			'conditions' => array(
				'merge_id' => 1
			)
		));
		$result = $request['MergeRequest']['merge_id'];
		$this->assertEqual($result, 1);
		
		$newUser = $this->Users->User->findByUsername('newusername');
		$result = $newUser['User']['id'];
		$this->assertEqual($request['MergeRequest']['model_id'], $result);
	}

	function testAdd() {
		$this->loadFixtures('MergeRequest', 'Address', 'Household', 'HouseholdMember', 'Notification', 'Invitation');
		$notificationsBefore = $this->Users->User->Notification->find('count');
		$invitesBefore = $this->Users->User->Invitation->find('count');

		$data = array(
			'User' => array(
				'username' => 'newusername'
			),
			'Address' => array(
				0 => array(
					'address_line_1' => '123 Main',
					'city' => 'Anytown',
					'state' => 'CA',
					'zip' => '12345'
				)
			),
			'Profile' => array(
				'first_name' => 'Test',
				'last_name' => 'User',
				'primary_email' => 'test@test.com'
			),
			'HouseholdMember' => array(
				0 => array(
					'Profile' => array(
						'first_name' => 'child',
						'last_name' => 'user',
						'primary_email' => 'child@example.com'
					)
				),
				1 => array(
					'Profile' => array(
						'first_name' => 'jeremy',
						'last_name' => 'harris'
					)
				)
			)
		);
		$vars = $this->testAction('/users/add/1', array(
			'data' => $data
		));

		$notificationsAfter = $this->Users->User->Notification->find('count');
		$invitesAfter = $this->Users->User->Invitation->find('count');
		$this->assertEqual($notificationsAfter-$notificationsBefore, 2);
		$this->assertEqual($invitesAfter-$invitesBefore, 1);

		$this->Users->User->contain(array('Profile'));
		$user = $this->Users->User->findByUsername('newusername');
		$result = $user['Profile']['name'];
		$this->assertEqual($result, 'Test User');
	}

	function testRegister() {
		$this->loadFixtures('MergeRequest', 'Address', 'Household', 'HouseholdMember', 'Notification', 'Invitation');
		$notificationsBefore = $this->Users->User->Notification->find('count');
		$invitesBefore = $this->Users->User->Invitation->find('count');

		$data = array(
			'User' => array(
				'username' => 'jharris'
			),
			'Address' => array(
				0 => array(
					'address_line_1' => '123 Main',
					'city' => 'Anytown',
					'state' => 'CA',
					'zip' => '12345'
				)
			),
			'Profile' => array(
				'first_name' => 'Test',
				'last_name' => 'User',
				'primary_email' => 'test@test.com',
				'birth_date' => array(
					'month' => 4,
					'day' => 14,
					'year' => 1984
				)
			),
			'HouseholdMember' => array(
				0 => array(
					'Profile' => array(
						'first_name' => 'child',
						'last_name' => 'user',
						'primary_email' => 'child@example.com',
						'birth_date' => array(
							'month' => 1,
							'day' => 2,
							'year' => date('Y')
						)
					)
				),
				1 => array(
					'Profile' => array(
						'first_name' => 'jeremy',
						'last_name' => 'harris'
					)
				)
			)
		);
		$vars = $this->testAction('/users/register/1', array(
			'data' => $data
		));

		$data['User']['username'] = 'newusername';
		$vars = $this->testAction('/users/register/1', array(
			'data' => $data
		));
		$notificationsAfter = $this->Users->User->Notification->find('count');
		$invitesAfter = $this->Users->User->Invitation->find('count');
		$this->assertEqual($notificationsAfter-$notificationsBefore, 2);
		$this->assertEqual($invitesAfter-$invitesBefore, 1);

		$this->Users->User->contain(array('Profile'));
		$user = $this->Users->User->findByUsername('newusername');
		$result = $user['Profile']['name'];
		$this->assertEqual($result, 'Test User');
		
		// get last invitation and make sure it has the right actions
		$this->Users->User->contain(array('Profile', 'HouseholdMember' => array('Household')));
		$user = $this->Users->User->read(null, 1);
		$invitations = $this->Users->User->Invitation->getInvitations(1);
		$invitation = $this->Users->User->Invitation->read(null, $invitations[count($invitations)-1]);
		$lastHousehold = $user['HouseholdMember'][count($user['HouseholdMember'])-1]['Household'];
		$results = $invitation['Invitation']['confirm_action'];
		$expected = '/households/confirm/1/'.$lastHousehold['id'];
		$this->assertEqual($results, $expected);
		$results = $invitation['Invitation']['deny_action'];
		$expected = '/households/delete/1/'.$lastHousehold['id'];
		$this->assertEqual($results, $expected);
		
		// test for no fuzzy username search
		$data = array(
			'User' => array(
				'username' => 'newuserna'
			),
			'Address' => array(
				0 => array(
					'address_line_1' => '123 Main',
					'city' => 'Anytown',
					'state' => 'CA',
					'zip' => '12345'
				)
			),
			'Profile' => array(
				'first_name' => 'Another',
				'last_name' => 'User',
				'primary_email' => 'test2@test.com',
				'birth_date' => array(
					'month' => 4,
					'day' => 14,
					'year' => 1984
				)
			)
		);
		$vars = $this->testAction('/users/register', array(
			'data' => $data
		));
		$this->Users->User->contain(array('Profile'));
		$user = $this->Users->User->findByUsername('newuserna');
		$result = $user['Profile']['name'];
		$this->assertEqual($result, 'Another User');
		
		/**
		 * Test redirections (setAction) based on various `findUser` responses
		 */
		
		$data = array(
			'Profile' => array(
				'first_name' => 'ricky',
				'last_name' => 'rock',
				'primary_email' => ''
			)
		);
		$vars = $this->testAction('/users/register', array(
			'data' => $data
		));
		$results = $this->testController->action;
		$expected = 'choose_user';
		$this->assertEqual($results, $expected);
		
		$data = array(
			'Profile' => array(
				'first_name' => 'ricky',
				'last_name' => 'rock',
				'primary_email' => 'jharris@rockharbor.org'
			)
		);
		$vars = $this->testAction('/users/register', array(
			'data' => $data
		));
		$results = $this->testController->action;
		$expected = '/users/register';
		$this->assertEqual($results, $expected);
		
		$data = array(
			'Profile' => array(
				'first_name' => 'jeremy',
				'last_name' => 'harris',
				'primary_email' => 'jharris@rockharbor.org'
			)
		);
		$vars = $this->testAction('/users/register', array(
			'data' => $data
		));
		$results = $this->testController->action;
		$expected = 'forgot_password';
		$this->assertEqual($results, $expected);
	}
	
	function testChooseUser() {
		$this->Users->choose_user(array(1, 2), '/users/request_activation/:ID:/1', '/original/action');
		$vars = $this->Users->viewVars;
		$this->assertEqual(count($vars['users']), 2);
		$this->assertEqual($vars['redirect'], FULL_BASE_URL.'/users/request_activation/:ID:/1');
		$this->assertEqual($vars['return'], FULL_BASE_URL.'/original/action/skip_check:1');
		
		$this->Users->choose_user(array(1, 2), array(
			'controller' => 'some_controller',
			'action' => 'action',
			':ID:'
		), '/original/action');
		$vars = $this->Users->viewVars;
		$this->assertEqual($vars['redirect'], FULL_BASE_URL.'/some_controller/action/:ID:');
	}
	
	function testHouseholdAdd() {
		$this->loadFixtures('MergeRequest', 'Address', 'Household', 'HouseholdMember', 'Notification', 'Invitation');
		$notificationsBefore = $this->Users->User->Notification->find('count');

		$data = array(
			'User' => array(
				'username' => 'jharris'
			),
			'Address' => array(
				0 => array(
					'zip' => '12345'
				)
			),
			'Profile' => array(
				'first_name' => 'Jeremy',
				'last_name' => 'Harris',
				'primary_email' => 'jeremy@paxtechservices.com',
				'birth_date' => array(
					'month' => 4,
					'day' => 14,
					'year' => 1984
				)
			),
			'Household' => array(
				'id' => 2
			)
		);
		// will redirect to choose_user because a user is found
		$vars = $this->testAction('/users/household_add/Household:2', array(
			'data' => $data
		));
		$this->assertEqual($vars['redirect'], FULL_BASE_URL.'/households/invite/:ID:/2');
		$this->assertEqual($this->testController->data['Household']['id'], 2);

		// simulate coming from choose_user when no user is chosen
		$data['User']['username'] = 'newusername';
		$data['Household']['id'] = 2;
		$vars = $this->testAction('/users/household_add/skip_check:1', array(
			'data' => $data
		));
		$notificationsAfter = $this->Users->User->Notification->find('count');
		$this->assertEqual($notificationsAfter-$notificationsBefore, 2);

		$this->Users->User->contain(array(
			'Profile', 
			'HouseholdMember' => array(
				'Household' => array(
					'HouseholdContact'
				)
			)
		));
		$user = $this->Users->User->findByUsername('newusername');
		$result = $user['Profile']['name'];
		$this->assertEqual($result, 'Jeremy Harris');
		
		// make sure they only have one household
		$this->assertEqual(count($user['HouseholdMember']), 1);
		
		// make sure they belong to user 1's household
		$result = $user['HouseholdMember'][0]['Household']['HouseholdContact']['id'];
		$this->assertEqual($result, 2);
		$result = $user['HouseholdMember'][0]['Household']['id'];
		$this->assertEqual($result, 2);
		
		$data = array(
			'Profile' => array(
				'first_name' => 'Ricky',
				'last_name' => 'Rockharbor',
				'primary_email' => null,
				'birth_date' => array(
					'year' => '',
					'month' => '',
					'day' => ''
				)
			),
			'Household' => array(
				'id' => 2
			)
		);
		// will redirect to choose_user because a user is found
		$vars = $this->testAction('/users/household_add/Household:2', array(
			'data' => $data
		));
		$this->assertEqual($vars['redirect'], FULL_BASE_URL.'/households/invite/:ID:/2');
		
		$results = Set::extract('/User/id', $vars['users']);
		$expected = array(2, 3);
		$this->assertEqual($results, $expected);
	}
	
	function testSpecialBirthdateValidation() {
		$origValidation = $this->Users->User->Profile->validate;
		
		$data = array(
			'User' => array(
				'username' => 'someuser'
			),
			'Address' => array(
				0 => array(
					'zip' => '12345'
				)
			),
			'Profile' => array(
				'first_name' => 'Some',
				'last_name' => 'User',
				'primary_email' => 'some@example.com'
			),
			'Household' => array(
				'id' => 2
			)
		);
		
		$vars = $this->testAction('/users/household_add/Household:2', array(
			'data' => $data
		));
		$result = array_keys($this->Users->User->Profile->validationErrors);
		$expected = array('birth_date');
		$this->assertEqual($result, $expected);
		
		$result = $this->Users->Session->read('Message.flash.element');
		$this->assertEqual($result, 'flash'.DS.'failure');
		
		$this->Users->User->Profile->validate = $origValidation;
		$vars = $this->testAction('/users/register', array(
			'data' => $data
		));
		$result = array_keys($this->Users->User->Profile->validationErrors);
		$expected = array('birth_date');
		$this->assertEqual($result, $expected);
		
		$result = $this->Users->Session->read('Message.flash.element');
		$this->assertEqual($result, 'flash'.DS.'failure');
		
		$this->Users->User->Profile->validate = $origValidation;
		$vars = $this->testAction('/users/add', array(
			'data' => $data
		));
		$result = array_keys($this->Users->User->Profile->validationErrors);
		$this->assertTrue(empty($result));
		
		$result = $this->Users->Session->read('Message.flash.element');
		$this->assertEqual($result, 'flash'.DS.'success');
		
		$data = array(
			'User' => array(
				'username' => 'someuseragain'
			),
			'Address' => array(
				0 => array(
					'zip' => '12345'
				)
			),
			'Profile' => array(
				'first_name' => 'Some',
				'last_name' => 'Again',
				'primary_email' => 'someuseragain@example.com',
				'birth_date' => array(
					'month' => '',
					'day' => '',
					'year' => ''
				)
			),
			'Household' => array(
				'id' => 2
			)
		);
		
		$this->Users->User->Profile->validate = $origValidation;
		$vars = $this->testAction('/users/household_add/Household:2', array(
			'data' => $data
		));
		$result = array_keys($this->Users->User->Profile->validationErrors);
		$expected = array('birth_date');
		$this->assertEqual($result, $expected);
		
		$result = $this->Users->Session->read('Message.flash.element');
		$this->assertEqual($result, 'flash'.DS.'failure');
		
		$this->Users->User->Profile->validate = $origValidation;
		$vars = $this->testAction('/users/register', array(
			'data' => $data
		));
		$result = array_keys($this->Users->User->Profile->validationErrors);
		$expected = array('birth_date');
		$this->assertEqual($result, $expected);
		
		$result = $this->Users->Session->read('Message.flash.element');
		$this->assertEqual($result, 'flash'.DS.'failure');
		
		$this->Users->User->Profile->validate = $origValidation;
		$vars = $this->testAction('/users/add', array(
			'data' => $data
		));
		$result = array_keys($this->Users->User->Profile->validationErrors);
		$this->assertTrue(empty($result));

		$result = $this->Users->Session->read('Message.flash.element');
		$this->assertEqual($result, 'flash'.DS.'success');
		
	}

}
