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
	
	function testMerge() {
		$this->loadFixtures('Profile', 'Address', 'Roster');
		
		$this->assertFalse($this->User->merge(1));
		$this->assertFalse($this->User->merge(1, 0));
		
		$user = array(
			'User' => array(
				'username' => 'jeremyharris'
			),
			'Address' => array(
					0 => array(
						'name' => 'Home',
						'address_line_1' => '3095 Red hill',
						'address_line_2' => '',
						'city' => 'Costa Mesa',
						'state' => 'CA',
						'zip' => 92626
					)
			),
			'Profile' => array(
				'first_name' => 'Jeremy',
				'last_name' => 'Schmarris',
				'primary_email' => 'test@example.com'
			)
		);
		$this->assertTrue($this->User->createUser($user));
		$newId = $this->User->id;
		
		$this->assertTrue($this->User->merge(1, $newId));
		$this->assertFalse($this->User->read(null, $newId));
		
		$results = $this->User->find('first', array(
			'conditions' => array(
				'User.id' => 1
			),
			'contain' => array(
				'Profile',
				'ActiveAddress',
				'Address',
				'Roster'
			)
		));
		$this->assertEqual($results['User']['id'], 1);
		$this->assertEqual($results['User']['username'], 'jeremyharris');
		$this->assertEqual($results['Profile']['id'], 1);
		$this->assertEqual($results['Profile']['first_name'], 'Jeremy');
		$this->assertEqual($results['Profile']['last_name'], 'Schmarris');
		$this->assertEqual($results['Profile']['user_id'], 1);
		$this->assertEqual($results['Profile']['alternate_email_1'], 'jeremy@paxtechservices.com');
		$this->assertEqual(count($results['Address']), 3);
		$this->assertTrue($results['ActiveAddress']['primary']);
		$this->assertTrue($results['ActiveAddress']['active']);
		$this->assertTrue(!empty($results['Roster']));
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
		$expected = '005b8f6046bb2039063d9dde0678f9f28ae38827';
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
		$expected = '005b8f6046bb2039063d9dde0678f9f28ae38827';
		$this->assertEqual($result, $expected);
	}

	function testFindUser() {
		$this->loadFixtures('Profile');

		$data = array(
			'User' => array(
				'username' => 'jharris'
			)
		);
		$result = $this->User->findUser($data);
		$expected = array(1);
		$this->assertEqual($result, $expected);

		$data = array(
			'Profile' => array(
				'first_name' => 'jeremy',
				'last_name' => 'harris'
			)
		);
		$result = $this->User->findUser($data);
		$expected = array(1);
		$this->assertEqual($result, $expected);
		
		$data = array(
			'User' => array(
				'Profile' => array(
					'first_name' => 'jeremy',
					'last_name' => 'harris'
				)
			)
		);
		$result = $this->User->findUser($data);
		$expected = array(1);
		$this->assertEqual($result, $expected);
		
		$data = array(
			'User' => array(
				'Profile' => array(
					'first_name' => 'jeremy',
					'last_name' => 'not harris'
				)
			)
		);
		$result = $this->User->findUser($data, 'OR');
		$expected = array();
		$this->assertEqual($result, $expected);
		
		$data = array(
			'User' => array(
				'Profile' => array(
					'first_name' => 'jeremy',
					'last_name' => 'harris'
				)
			)
		);
		$result = $this->User->findUser($data, 'OR');
		$expected = array(1);
		$this->assertEqual($result, $expected);

		$data = array(
			'User' => array(
				'username' => 'rickyrockharbor'
			),
			'Profile' => array(
				'email' => 'jeremy@paxtechservices.com',
				'last_name' => 'harris'
			)
		);
		$result = $this->User->findUser($data, 'OR');
		$expected = array(1, 2, 3);
		$this->assertEqual($result, $expected);
		
		$data = array(
			'Profile' => array(
				'birth_date' => array(
					'year' => '1984',
					'month' => '04',
					'day' => '14'
				)
			)
		);
		$result = $this->User->findUser($data);
		$expected = array(1);
		$this->assertEqual($result, $expected);
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
				'first_name' => 'Another',
				'last_name' => 'User',
				'primary_email' => 'test@example.com'
			),
			'HouseholdMember' => array(
				0 => array(
					'Profile' => array(
						'first_name' => 'child',
						'last_name' => ''
					)
				)
			)
		);		
		$this->assertFalse($this->User->createUser($user, null, $creator));
		$this->assertEqual(count($this->User->tmpAdded), 0);

		$expected = array('last_name');
		$this->assertEqual(array_keys($this->User->HouseholdMember->validationErrors[0]['Profile']), $expected);

		$this->User->tmpAdded = $this->User->tmpInvited = array();
		$user = array(
			'User' => array(
				'username' => 'testme2',
				'password' => 'password2'
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
				'last_name' => 'User2',
				'primary_email' => 'another2@example.com'
			),
			'HouseholdMember' => array(
				0 => array(
					'Profile' => array(
						'first_name' => '',
						'last_name' => '',
						'primary_email' => ''
					)
				)
			)
		);
		$this->assertTrue($this->User->createUser($user, null, $creator));
		$this->assertEqual(count($this->User->tmpAdded), 1);

		$this->User->tmpAdded = $this->User->tmpInvited = array();
		$user = array(
			'Address' => array(
				0 => array(
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
					'User' => array(
						'id' => 1
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
		
		$this->User->tmpAdded = $this->User->tmpInvited = array();
		$user = array(
			'Address' => array(
				0 => array(
					'zip' => 92886
				)
			),
			'Profile' => array(
				'first_name' => 'Yet Another',
				'last_name' => 'User',
				'primary_email' => 'another3@example.com'
			),
			'HouseholdMember' => array(
				0 => array(
					'Profile' => array(
						'last_name' => 'rockharbor'
					)
				),
				1 => array(
					'Profile' => array(
						'last_name' => 'harris'
					)
				)
			)
		);
		$this->assertFalse($this->User->createUser($user, null, $creator));
		
		$expected = array(
			0 => array(
				'found' => array( // multiple accounts found
					array(
						'User' => array(
							'id' => 2
						),	
						'Profile' => array(
							'id' => 2,
							'first_name' => 'ricky',
							'last_name' => 'rockharbor'
						),
						'ActiveAddress' => array(
							'city' => null
						)
					),
					array(
						'User' => array(
							'id' => 3
						),	
						'Profile' => array(
							'id' => 3,
							'first_name' => 'ricky jr.',
							'last_name' => 'rockharbor'
						),
						'ActiveAddress' => array(
							'city' => null
						)
					)
				),
				'Profile' => array(
					'last_name' => 'rockharbor' // persisted data
				)
			),
			1 => array( // single account found
				'User' => array(
					'id' => 1
				),
				'Profile' => array( 
					'id' => 1,
					'first_name' => 'Jeremy',
					'last_name' => 'Harris'
				),
				'ActiveAddress' => array(
					'city' => 'Orange'
				)
			)
		);
		$this->assertEqual($user['HouseholdMember'], $expected);
		
		$this->User->tmpAdded = $this->User->tmpInvited = array();
		$user = array(
			'User' => array(
				'username' => 'jharris'
			),
			'Address' => array(
				0 => array(
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
					'User' => array(
						'id' => 1
					)
				)
			)
		);
		$this->assertFalse($this->User->createUser($user, null, $creator, false));
		$this->assertTrue(isset($this->User->validationErrors['username']));
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
			'link' => array(
				'Address' => array()
			),
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
		
		$search = array(
			'Search' => array(
				'operator' => 'OR'
			),
			'User' => array(
				'username' => null,
				'active' => 1,
				'flagged' => null
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
					'User.active' => true
				)
			)
		);
		$this->assertEqual($results, $expected);
		
		$search = array(
			'Search' => array(
				'operator' => 'AND'
			),
			'User' => array(
				 'username' => 'j'
			),
			'Profile' => array(
				'age' => array(
					 '0-99'
				),
				'gender' => 'm'
			),
			'Roster' => array(
				'Involvement' => array(
					'name' => 'Core'
				)
			)
		);
		$results = $this->User->prepareSearch($this->Controller, $search);
		$expected = array(
			'link' => array(
				'Profile' => array(
					'fields' => array(
						'user_id',
						$this->User->Profile->getVirtualField('age').' AS Profile__age',
						'gender'
					)
				),
				'Roster' => array(
					'fields' => array(
						'user_id', 'id', 'involvement_id'
					),
					'Involvement' => array(
						'fields' => array(
							'name'
						)
					)
				)
			),
			'group' => 'User.id',
			'conditions' => array(
				'Profile.gender LIKE' => '%m%',
				'User.username LIKE' => '%j%',
				'Involvement.name LIKE' => '%Core%',
				array(
					'or' => array(
						 array(
							  $this->User->Profile->getVirtualField('age').' BETWEEN ? AND ?' => array(0, 99)
						 )
					)
				)
			)
		);
		$this->assertEqual($results, $expected);
		
		$search = array(
			'Search' => array(
				'operator' => 'AND'
			),
			'Profile' => array(
				'birth_date' => '4/14/1984'
			)
		);
		$results = $this->User->prepareSearch($this->Controller, $search);
		$expected = array(
			'link' => array(
				'Profile' => array(
					'fields' => array(
						'user_id', 'birth_date'
					)
				)
			),
			'group' => 'User.id',
			'conditions' => array(
				'Profile.birth_date' => '1984-04-14',
			)
		);
		$this->assertEqual($results, $expected);
		
		$search = array(
			'Search' => array(
				'operator' => 'AND'
			),
			'Profile' => array(
				'birth_date' => array(
					'month' => '04',
					'day' => '14',
					'year' => '1984'
				)
			)
		);
		$results = $this->User->prepareSearch($this->Controller, $search);
		$expected = array(
			'link' => array(
				'Profile' => array(
					'fields' => array(
						'user_id', 'birth_date'
					)
				)
			),
			'group' => 'User.id',
			'conditions' => array(
				'Profile.birth_date' => '1984-04-14',
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