<?php
/* User Test cases generated on: 2010-06-28 09:06:40 : 1277741500*/
App::import('Lib', 'CoreTestCase');
App::import('Model', 'User');
App::import('Controller', 'App');

class UsersTestController extends AppController {

}

class UserTestCase extends CoreTestCase {
	
	function startTest() {
		$this->loadFixtures('User', 'Group');
		$this->User =& ClassRegistry::init('User');
	}

	function endTest() {
		unset($this->User);
		ClassRegistry::flush();
	}

	function testHashPasswords() {
		// as if sent by auth (login)
		$this->User->data = array();
		$data = array(
			'User' => array(
				'password' => 'password'
			)
		);
		$data = $this->User->hashPasswords($data);
		$result = $data['User']['password'];
		$expected = 'e8b43e0909e7adcda8d78698bf144ec517568ccc';
		$this->assertEqual($result, $expected);

		// as if sent by edit but doesn't validate
		$this->User->data = array();
		$data = array(
			'User' => array(
				'password' => 'password',
				'confirm_password' => 'password'
			)
		);
		$data = $this->User->hashPasswords($data);
		$result = $data['User']['password'];
		$expected = 'password';
		$this->assertEqual($result, $expected);

		// as if sent by edit and validates
		$this->User->data = array(
			'User' => array(
				'password' => 'password',
				'confirm_password' => 'password'
			)
		);
		$data = $this->User->hashPasswords(null, true);
		$result = $data['User']['password'];
		$expected = 'e8b43e0909e7adcda8d78698bf144ec517568ccc';
		$this->assertEqual($result, $expected);
	}

	function testFindUser() {
		$this->loadFixtures('Profile');

		$result = $this->User->findUser('jharris');
		$expected = 1;
		$this->assertEqual($result, $expected);

		$result = $this->User->findUser(array('jharris'));
		$expected = 1;
		$this->assertEqual($result, $expected);

		$result = $this->User->findUser(array('jeremy', 'harris'));
		$expected = 1;
		$this->assertEqual($result, $expected);

		$result = $this->User->findUser(array('jeremy@paxtechservices.com', 'rickyrockharbor'));
		$this->assertFalse($result);
	}

	function testCreateUser() {
		$this->loadFixtures('Address', 'Profile', 'Household', 'HouseholdMember');

		$creator = array(
			'User' => array(
				'id' => 10,
				'username' => 'mrcreator',
				'group_id' => 1
			)
		);
		
		$user = array(
			'Address' => array(
					0 => array(
						'name' => 'Work',
						'address_line_1' => '3080 Airway',
						'address_line_2' => '',
						'city' => 'Costa Mesa',
						'state' => 'CA',
						'zip' => 92886
					)
			),
			'Profile' => array(
				'first_name' => 'Test',
				'last_name' => 'User',
				'primary_email' => 'test@example.com'
			)
		);		
		$this->assertTrue($this->User->createUser($user, null, $creator));
		$this->assertEqual(count($this->User->tmpAdded), 1);
		$user = $this->User->read('reset_password', $this->User->tmpAdded[0]['id']);
		$this->assertTrue($user['User']['reset_password']);

		$this->User->tmpAdded = $this->User->tmpInvited = array();
		$user = array(
			'Address' => array(
					0 => array(
						'name' => 'Work',
						'address_line_1' => '3080 Airway',
						'address_line_2' => '',
						'city' => 'Costa Mesa',
						'state' => 'CA',
						'zip' => 92886
					)
			),
			'Profile' => array(
				'first_name' => 'Test',
				'last_name' => 'User',
				'primary_email' => 'test@example.com'
			)
		);
		$this->assertFalse($this->User->createUser($user, null, $creator));
		$this->assertEqual(count($this->User->tmpAdded), 0);

		$this->User->tmpAdded = $this->User->tmpInvited = array();
		$user = array(
			'Address' => array(
					0 => array(
						'name' => 'Work',
						'address_line_1' => '3080 Airway',
						'address_line_2' => '',
						'city' => 'Costa Mesa',
						'state' => 'CA',
						'zip' => 92886
					)
			),
			'Profile' => array(
				'first_name' => 'Another',
				'last_name' => 'User',
				'primary_email' => 'test@example.com'
			),
			'HouseholdMember' => array(
				0 => array(
					'Profile' => array(
						'first_name' => 'child',
						'last_name' => 'user'
					)
				)
			)
		);		
		$this->assertFalse($this->User->createUser($user, null, $creator));
		$this->assertEqual(count($this->User->tmpAdded), 0);

		$this->User->tmpAdded = $this->User->tmpInvited = array();
		$user = array(
			'User' => array(
				'username' => 'testme',
				'password' => 'password'
			),
			'Address' => array(
					0 => array(
						'name' => 'Work',
						'address_line_1' => '3080 Airway',
						'address_line_2' => '',
						'city' => 'Costa Mesa',
						'state' => 'CA',
						'zip' => 92886
					)
			),
			'Profile' => array(
				'first_name' => 'Yet Another',
				'last_name' => 'User',
				'primary_email' => 'another@example.com'
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
		$this->assertTrue($this->User->createUser($user, null, $creator));
		$this->assertEqual(count($this->User->tmpAdded), 2);
		$this->assertEqual(count($this->User->tmpInvited), 1);
		$user = $this->User->read('reset_password', $this->User->tmpAdded[0]['id']);
		$this->assertTrue($user['User']['reset_password']);
		$user = $this->User->read('reset_password', $this->User->tmpAdded[1]['id']);
		$this->assertTrue($user['User']['reset_password']);
	}

	function testPrepareSearch() {
		$this->loadFixtures('Address', 'Profile');

		$this->Controller = new UsersTestController();

		$search = array(
			'Search' => array(
				'operator' => 'OR'
			),
			'User' => array(
				'username' => 'jharris'
			),
			'Profile' => array(
				'Birthday' => array(),
				'email' => array()
			),
			'Distance' => array()
		);
		$results = $this->User->prepareSearch($this->Controller, $search);
		$expected = array(
			'link' => array(),
			'group' => 'User.id',
			'conditions' => array(
				'OR' => array(
					'User.username LIKE' => '%jharris%'
				)
			)
		);
		$this->assertEqual($results, $expected);

		$search['User'] = array();
		$search['Distance']['distance_from'] = 92868;
		$search['Distance']['distance'] = 5;
		$results = $this->User->prepareSearch($this->Controller, $search);
		$expected = array(
			'link' => array(),
			'group' => 'User.id',
			'conditions' => array(
				'OR' => array(
					'Address.id' => array(2)
				)
			)
		);
		$this->assertEqual($results, $expected);

		$search = array(
			'User' => array(
				'username' => 'jharris'
			)
		);
		$results = $this->User->prepareSearch($this->Controller, $search);
		$expected = array(
			'link' => array(),
			'group' => 'User.id',
			'conditions' => array(
				'User.username LIKE' => '%jharris%'
			)
		);
		$this->assertEqual($results, $expected);
	}

	function testGenerateUsername() {
		$result = $this->User->generateUsername();
		$expected = '';
		$this->assertEqual($result, $expected);

		$result = $this->User->generateUsername('mark', 'story');
		$expected = 'markstory';
		$this->assertEqual($result, $expected);

		$result = $this->User->generateUsername('j', 'harris');
		$this->assertPattern('/jharris([0-9]{1})/', $result);

		for ($i=0; $i<10; $i++) {
			$this->User->saveAll(array(
				array(
					'User' => array(
						'username' => 'jharris'.$i,
						'password' => 'password'
					)
				)
			), array('validate' => false));
		}

		$result = $this->User->generateUsername('j', 'harris');
		$this->assertPattern('/jharris([0-9]{2})/', $result);
		
	}

	function testGeneratePassword() {
		$result = $this->User->generatePassword();
		$this->assertTrue(is_string($result));
	}

}
?>