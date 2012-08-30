<?php
/* Searches Test cases generated on: 2010-08-04 13:08:57 : 1280952657 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail.QueueEmail'));
App::import('Controller', 'Searches');

Mock::generatePartial('QueueEmailComponent', 'MockSearchesQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('SearchesController', 'MockSearchesController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class SearchesControllerTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('User', 'Ministry', 'Involvement', 'Profile', 'InvolvementType', 'Group', 'Campus');
		$this->Searches =& new MockSearchesController();
		$this->Searches->__construct();
		$this->Searches->constructClasses();
		$this->Searches->FilterPagination->initialize($this->Searches);
		$this->Searches->Notifier->QueueEmail = new MockSearchesQueueEmailComponent();
		$this->Searches->Notifier->QueueEmail->enabled = true;
		$this->Searches->Notifier->QueueEmail->initialize($this->Searches);
		$this->Searches->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Searches->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->Searches->setReturnValue('isAuthorized', true);
		$this->testController = $this->Searches;
		$this->loadSettings();
	}

	function endTest() {
		$this->unloadSettings();
		$this->Searches->Session->destroy();
		unset($this->Searches);
		ClassRegistry::flush();
	}

	function testIndex() {
		$this->loadFixtures('Roster');
		
		$vars = $this->testAction('/searches/index?q=rick');
		$results = Set::extract('/User/username', $vars['users']);
		$expected = array(
			'rickyrockharbor',
			'rickyrockharborjr'
		);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/searches/index?q=alpha');
		$results = Set::extract('/Ministry/name', $vars['ministries']);
		$expected = array(
			'Alpha',
		);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/searches/index?q=core+2.0+test');
		$results = Set::extract('/Involvement/name', $vars['involvements']);
		$expected = array(
			'CORE 2.0 testing'
		);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/searches/index/model:User?q=rick');
		$results = Set::extract('/User/username', $vars['users']);
		$expected = array(
			'rickyrockharbor',
			'rickyrockharborjr'
		);
		$this->assertEqual($results, $expected);
		$this->assertEqual($vars['ministries'], array());		
		$this->assertEqual($vars['involvements'], array());
		
		$vars = $this->testAction('/searches?q=comm', array(
			'data' => array(
				'Search' => array(
					'Campus' => array(
						'id' => array(2)
					)
				)
			)
		));
		$results = Set::extract('/Ministry/name', $vars['ministries']);
		$expected = array();
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/searches?q=jeremy', array(
			'data' => array(
				'Search' => array(
					'Campus' => array(
						'id' => array(2)
					)
				)
			)
		));
		$results = Set::extract('/User/id', $vars['ministries']);
		$expected = array();
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/searches?q=fullerton', array(
			'data' => array(
				'Search' => array(
					'Campus' => array(
						'id' => array(2)
					),
					'active' => 1,
					'private' => 0
				)
			)
		));
		$results = Set::extract('/Ministry/name', $vars['ministries']);
		$this->assertEqual($results, array());
		$results = Set::extract('/Involvement/name', $vars['involvements']);
		$expected = array(
			'Fullerton meetup'
		);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/searches?q=core', array(
			'data' => array(
				'Search' => array(
					'Ministry' => array(
						'id' => 4
					)
				)
			)
		));
		$results = Set::extract('/Ministry/name', $vars['ministries']);
		$expected = array();
		$this->assertEqual($results, $expected);
		$results = Set::extract('/Involvement/name', $vars['involvements']);
		$expected = array(
			 'CORE 2.0 testing', 'Team CORE'
		);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/searches?q=downtown', array(
			'data' => array(
				'Search' => array(
					'Campus' => array(
						'id' => array(2)
					),
					'private' => 0
				)
			)
		));
		$results = Set::extract('/Ministry/name', $vars['ministries']);
		$this->assertEqual($results, array());
	}

	function testInvolvement() {
		$this->loadFixtures('Address');
		
		$vars = $this->testAction('/searches/involvement?q=core');
		$results = Set::extract('/Involvement/name', $vars['results']);
		$expected = array(
			'CORE 2.0 testing',
			'Team CORE'
		);
		$this->assertEqual($results, $expected);
		
		$search = array(
			'Involvement' => array(
				'name' => 'core'
			)
		);
		$vars = $this->testAction('/searches/involvement', array(
			'data' => $search
		));
		$results = Set::extract('/Involvement/name', $vars['results']);
		$expected = array(
			'CORE 2.0 testing',
			'Team CORE'
		);
		$this->assertEqual($results, $expected);

		$search = array(
			'Involvement' => array(
				'description' => 'core'
			)
		);
		$vars = $this->testAction('/searches/involvement', array(
			'data' => $search
		));
		$results = Set::extract('/Involvement/name', $vars['results']);
		$expected = array(
			'Team CORE'
		);
		$this->assertEqual($results, $expected);
		
		$search = array(
			'Involvement' => array(
				'name' => 'core',
				'inactive' => 1,
				'private' => 0
			)
		);
		$vars = $this->testAction('/searches/involvement', array(
			'data' => $search
		));
		$results = Set::extract('/Involvement/name', $vars['results']);
		$expected = array(
			'CORE 2.0 testing'
		);
		$this->assertEqual($results, $expected);
		
		$search = array(
			'Involvement' => array(
				'name' => 'core',
				'inactive' => 1,
				'private' => 1
			)
		);
		$vars = $this->testAction('/searches/involvement', array(
			'data' => $search
		));
		$results = Set::extract('/Involvement/name', $vars['results']);
		$expected = array(
			'CORE 2.0 testing',
			'Team CORE'
		);
		$this->assertEqual($results, $expected);
		
		// as a regular user
		$this->su(array(
			'Group' => array('id' => 8)
		));
		
		$search = array(
			'Involvement' => array(
				'name' => 'core'
			)
		);
		$vars = $this->testAction('/searches/involvement', array(
			'data' => $search
		));
		$results = Set::extract('/Involvement/name', $vars['results']);
		$expected = array(
			'CORE 2.0 testing'
		);
		$this->assertEqual($results, $expected);
		
		$search = array(
			'Involvement' => array(
				'name' => 'core'
			),
			'Address' => array(
				'zip' => ''
			)
		);
		$vars = $this->testAction('/searches/involvement', array(
			'data' => $search
		));
		$results = Set::extract('/Involvement/name', $vars['results']);
		$expected = array(
			'CORE 2.0 testing'
		);
		$this->assertEqual($results, $expected);
		
		$search = array(
			'Involvement' => array(
				'previous' => 1
			),
			'Distance' => array(
				'distance_from' => 'new york, new york',
				'distance' => 10
			)
		);
		$vars = $this->testAction('/searches/involvement', array(
			'data' => $search
		));
		$results = Set::extract('/Involvement/id', $vars['results']);
		$expected = array();
		$this->assertEqual($results, $expected);
		
		$search['Distance']['distance_from'] = 'costa mesa, ca';
		$vars = $this->testAction('/searches/involvement', array(
			'data' => $search
		));
		$results = Set::extract('/Involvement/id', $vars['results']);
		$expected = array(1);
		$this->assertEqual($results, $expected);
		
		$search['Involvement']['name'] = 'nurture';
		$search['Distance']['distance_from'] = 'costa mesa, ca';
		$vars = $this->testAction('/searches/involvement', array(
			'data' => $search
		));
		$results = Set::extract('/Involvement/id', $vars['results']);
		$expected = array();
		$this->assertEqual($results, $expected);
	}

	function testMinistry() {
		$vars = $this->testAction('/searches/ministry?q=web');
		$results = Set::extract('/Ministry/name', $vars['results']);
		sort($results);
		$expected = array(
			'Child Web', 'Web'
		);
		$this->assertEqual($results, $expected);
		
		$search = array(
			'Ministry' => array(
				'name' => 'web'
			)
		);
		$vars = $this->testAction('/searches/ministry', array(
			'data' => $search
		));
		$results = Set::extract('/Ministry/name', $vars['results']);
		sort($results);
		$expected = array(
			'Child Web', 'Web'
		);
		$this->assertEqual($results, $expected);
		
		$search = array(
			'Ministry' => array(
				'name' => '',
				'private' => 0
			)
		);
		$vars = $this->testAction('/searches/ministry', array(
			'data' => $search
		));
		$results = Set::extract('/Ministry/id', $vars['results']);
		sort($results);
		$expected = array(1, 2, 3, 4, 6);
		$this->assertEqual($results, $expected);
		
		// as a regular user
		$this->su(array(
			'Group' => array('id' => 8)
		));
		
		$search = array(
			'Ministry' => array(
				'name' => ''
			)
		);
		$vars = $this->testAction('/searches/ministry', array(
			'data' => $search
		));
		$results = Set::extract('/Ministry/id', $vars['results']);
		sort($results);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);
	}

	function testUser() {
		$search = array(
			'Profile' => array(
				'email' => 'rockharbor'
			)
		);
		$vars = $this->testAction('/searches/user', array(
			'data' => $search
		));
		$results = Set::extract('/User/username', $vars['results']);
		$expected = array(
			'jharris',
			'joe',
			'rickyrockharbor',
			'rickyrockharborjr'
		);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/searches/user/page:2', array(
			'data' => $search
		));
	}

	function testSimpleSearch() {
		$data = array(
			'User' => array(
				'username' => 'a'
			)
		);
		$vars = $this->testAction('/searches/simple/User/some_element/', compact('data'));
		$results = Set::extract('/User/username', $vars['results']);
		$expected = array(
			'jharris',
			'rickyrockharbor',
			'rickyrockharborjr'
		);
		$this->assertEqual($results, $expected);
		$this->Searches->Session->delete('FilterPagination');

		$data = array(
			'User' => array(
				'username' => 'a'
			),
			'Profile' => array(
				'first_name' => 'jeremy'
			)
		);
		$vars = $this->testAction('/searches/simple/User/some_element/', compact('data'));
		$results = Set::extract('/User/username', $vars['results']);
		$expected = array(
			'jharris',
		);
		$this->assertEqual($results, $expected);
		$this->Searches->Session->delete('FilterPagination');
		
		$data = array(
			'Profile' => array(
				'primary_email' => 'ricky@'
			)
		);
		$vars = $this->testAction('/searches/simple/User/some_element/', compact('data'));
		$results = Set::extract('/User/username', $vars['results']);
		$expected = array(
			'rickyrockharbor',
		);
		$this->assertEqual($results, $expected);
		$this->Searches->Session->delete('FilterPagination');
	}

	function testNotInHouseholdSearchFilter() {
		$this->loadFixtures('Household', 'HouseholdMember');

		$data = array(
			'User' => array(
				'username' => 'jharris'
			)
		);
		$vars = $this->testAction('/searches/simple/User/some_element/notInHousehold/2', compact('data'));
		$results = Set::extract('/User/username', $vars['results']);
		$expected = array(
			'jharris',
		);
		$this->assertEqual($results, $expected);
		$this->Searches->Session->delete('FilterPagination');

		$data = array(
			'User' => array(
				'username' => 'a'
			)
		);
		$vars = $this->testAction('/searches/simple/User/some_element/notInHousehold/1', compact('data'));
		$results = Set::extract('/User/username', $vars['results']);
		$expected = array(
			'rickyrockharbor',
		);
		$this->assertEqual($results, $expected);
		$this->Searches->Session->delete('FilterPagination');
	}

	function testNotLeaderOfSearchFilter() {
		$this->loadFixtures('Leader');

		$data = array(
			'User' => array(
				'username' => ''
			)
		);
		$vars = $this->testAction('/searches/simple/User/some_element/notLeaderOf/Involvement/1', compact('data'));
		$results = Set::extract('/User/username', $vars['results']);
		$expected = array(
			'rickyrockharbor',
		);
		$this->assertEqual($results, $expected);
		$this->Searches->Session->delete('FilterPagination');

			$data = array(
			'User' => array(
				'username' => ''
			)
		);
		$vars = $this->testAction('/searches/simple/User/some_element/notLeaderOf/Involvement/20', compact('data'));
		$results = Set::extract('/User/username', $vars['results']);
		$expected = array(
			'jharris',
			'rickyrockharbor',
		);
		$this->assertEqual($results, $expected);
		$this->Searches->Session->delete('FilterPagination');
	}

	function testNotSignedUpSearchFilter() {
		$this->loadFixtures('Roster');

		$data = array(
			'User' => array(
				'username' => ''
			)
		);
		$vars = $this->testAction('/searches/simple/User/some_element/notSignedUp/1', compact('data'));
		$results = Set::extract('/User/username', $vars['results']);
		$expected = array(
			'bob',
			'jharris',
			'joe'
		);
		$this->assertEqual($results, $expected);
		$this->Searches->Session->delete('FilterPagination');

		$data = array(
			'User' => array(
				'username' => ''
			)
		);
		$vars = $this->testAction('/searches/simple/User/some_element/notSignedUp/20', compact('data'));
		$results = Set::extract('/User/username', $vars['results']);
		$expected = array(
			'bob',
			'jharris',
			'joe',
			'rickyrockharbor',
			'rickyrockharborjr'
		);
		$this->assertEqual($results, $expected);
		$this->Searches->Session->delete('FilterPagination');
	}

	function testNotInvolvementAndIsLeading() {
		$this->loadFixtures('Leader');

		$data = array(
			'Involvement' => array(
				'name' => ''
			)
		);
		$vars = $this->testAction('/searches/simple/Involvement/some_element/notInvolvementAndIsLeading/1/1', compact('data'));
		$results = Set::extract('/Involvement/name', $vars['results']);
		$expected = array(
			'Team CORE',
		);
		$this->assertEqual($results, $expected);
		$this->Searches->Session->delete('FilterPagination');

		$data = array(
			'Involvement' => array(
				'name' => 'wed'
			)
		);
		$vars = $this->testAction('/searches/simple/Involvement/some_element/notInvolvementAndIsLeading/3/1', compact('data'));
		$results = Set::extract('/Involvement/name', $vars['results']);
		$expected = array(
		);
		$this->assertEqual($results, $expected);
		$this->Searches->Session->delete('FilterPagination');

		$data = array(
			'Involvement' => array(
				'name' => 'core'
			)
		);
		$vars = $this->testAction('/searches/simple/Involvement/some_element/notInvolvementAndIsLeading/3/1', compact('data'));
		$results = Set::extract('/Involvement/name', $vars['results']);
		$expected = array(
			'CORE 2.0 testing'
		);
		$this->assertEqual($results, $expected);
		$this->Searches->Session->delete('FilterPagination');
	}

}
