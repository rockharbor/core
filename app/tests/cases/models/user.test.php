<?php
/* User Test cases generated on: 2010-06-28 09:06:40 : 1277741500*/
App::import('Model', 'User');
App::import('Controller', 'App');

class UsersTestController extends AppController {

}

class UserTestCase extends CakeTestCase {
	var $fixtures = array(
		'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category',
		'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement',
		'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date',
		'app.payment_option', 'app.question', 'app.roster', 'app.role',
		'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type',
		'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member',
		'app.household', 'app.publication', 'app.publications_user', 'app.log', 'app.ministries_rev', 'app.involvements_rev'
	);

	function startTest() {
		$this->loadFixtures('User');
		$this->User =& ClassRegistry::init('User');
	}

	function endTest() {
		unset($this->User);
		ClassRegistry::flush();
	}

	function testFindUser() {
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
	}

	function testPrepareSearch() {
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