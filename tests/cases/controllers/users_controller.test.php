<?php
/* Users Test cases generated on: 2010-08-11 07:08:43 : 1281537883 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail.QueueEmail', 'Notifier'));
App::import('Controller', 'Users');

Mock::generatePartial('QueueEmailComponent', 'MockQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('NotifierComponent', 'MockNotifierComponent', array('_render'));
Mock::generatePartial('UsersController', 'MockUsersController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class UsersControllerTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('User', 'Profile', 'Group');
		$this->loadSettings();
		$this->Users =& new MockUsersController();
		$this->Users->__construct();
		$this->Users->constructClasses();
		$this->Users->Notifier = new MockNotifierComponent();
		$this->Users->Notifier->initialize($this->Users);
		$this->Users->Notifier->setReturnValue('_render', 'Notification body text');
		$this->Users->Notifier->QueueEmail = new MockQueueEmailComponent();
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
				'current_password' => 'password',
				'confirm_password' => 'not confirmed!'
			)
		);
		$vars = $this->testAction('/users/edit/User:1', array(
			'data' => $data
		));
		$user = $this->Users->User->read(null, 1);
		$result = $user['User']['password'];
		$expected = $this->Users->Auth->password('password');
		$this->assertEqual($result, $expected);

		$result = $this->Users->Session->read('Message.flash.element');
		$this->assertEqual($result, 'flash'.DS.'failure');

		$data = array(
			'User' => array(
				'id' => 1,
				'reset' => 'password',
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

		$result = $vars['password'];
		$this->assertEqual($result, 'newpassword');

		$result = $this->Users->Session->read('Message.flash.element');
		$this->assertEqual($result, 'flash'.DS.'success');
	}

	function testLogin() {
		$lastLoggedIn = $this->Users->User->read('last_logged_in', 1);

		// trick CoreTestCase into not setting up a user
		$this->Users->Session->write('User', true);
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
		$this->assertNotEqual($result, $lastLoggedIn['User']['last_logged_in']);

		$this->Users->Session->delete('User');
		$this->Users->Session->delete('Auth');
	}

	function testForgotPassword() {
		$oldPassword = $this->Users->User->read('password', 1);
		$data = array(
			'User' => array(
				'forgotten' => 'jeremy harris'
			)
		);
		$vars = $this->testAction('/users/forgot_password', array(
			'data' => $data
		));
		$result = $vars['password'];
		$this->assertNotEqual($oldPassword['User']['password'], $result);
		$this->assertEqual($this->Users->Session->read('Message.flash.element'), 'flash'.DS.'success');
		$user = $this->Users->User->read('reset_password', 1);
		$this->assertTrue($user['User']['reset_password']);
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
				'model_id' => 1
			)
		));
		$result = $request['MergeRequest']['merge_id'];
		$this->assertEqual($result, $oldCount+1);
	}

	function testAdd() {
		$this->loadFixtures('MergeRequest', 'Address', 'Household', 'HouseholdMember', 'Notification');
		$notificationsBefore = $this->Users->User->Notification->find('count');

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
		$this->assertEqual($notificationsAfter-$notificationsBefore, 3);

		$this->Users->User->contain(array('Profile'));
		$user = $this->Users->User->findByUsername('newusername');
		$result = $user['Profile']['name'];
		$this->assertEqual($result, 'Test User');
	}

	function testRegister() {
		$this->loadFixtures('MergeRequest', 'Address', 'Household', 'HouseholdMember', 'Notification');
		$notificationsBefore = $this->Users->User->Notification->find('count');

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
				'primary_email' => 'test@test.com'
			),
			'HouseholdMember' => array(
				0 => array(
					'Profile' => array(
						'first_name' => 'child',
						'last_name' => 'user',
						'primary_email' => 'child@example.com'
					)
				)
			)
		);
		$vars = $this->testAction('/users/register/1', array(
			'data' => $data
		));

		$result = $vars['foundId'][0];
		$this->assertEqual($result, 1);

		$data['User']['username'] = 'newusername';
		$vars = $this->testAction('/users/register/1', array(
			'data' => $data
		));
		$notificationsAfter = $this->Users->User->Notification->find('count');
		$this->assertEqual($notificationsAfter-$notificationsBefore, 2);

		$this->Users->User->contain(array('Profile'));
		$user = $this->Users->User->findByUsername('newusername');
		$result = $user['Profile']['name'];
		$this->assertEqual($result, 'Test User');
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
		$this->loadFixtures('MergeRequest', 'Address', 'Household', 'HouseholdMember', 'Notification');
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
				'first_name' => 'Test',
				'last_name' => 'User',
				'primary_email' => 'test@test.com'
			),
			'Household' => array(
				'id' => 2
			)
		);
		// will redirect to choose_user because a user is found
		$vars = $this->testAction('/users/household_add/Household:2', array(
			'data' => $data
		));
		$this->assertEqual($vars['redirect'], FULL_BASE_URL.'/households/shift_households/:ID:/2');
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
		$this->assertEqual($result, 'Test User');
		
		// make sure they only have one household
		$this->assertEqual(count($user['HouseholdMember']), 1);
		
		// make sure they belong to user 1's household
		$result = $user['HouseholdMember'][0]['Household']['HouseholdContact']['id'];
		$this->assertEqual($result, 2);
		$result = $user['HouseholdMember'][0]['Household']['id'];
		$this->assertEqual($result, 2);
	}

}
?>