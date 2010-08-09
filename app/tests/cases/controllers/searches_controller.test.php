<?php
/* Searches Test cases generated on: 2010-08-04 13:08:57 : 1280952657 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail'));
App::import('Controller', 'Searches');

Mock::generate('QueueEmailComponent');
Mock::generatePartial('SearchesController', 'MockSearchesController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class SearchesControllerTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('User', 'Ministry', 'Involvement', 'Profile', 'InvolvementType');
		$this->Searches =& new MockSearchesController();
		$this->Searches->constructClasses();
		$this->Searches->FilterPagination->initialize($this->Searches);
		$this->Searches->QueueEmail = new MockQueueEmailComponent();
		$this->Searches->QueueEmail->setReturnValue('send', true);
		$this->testController = $this->Searches;
	}

	function endTest() {
		$this->Searches->Session->destroy();
		unset($this->Searches);
		ClassRegistry::flush();
	}

	function testIndex() {
		$vars = $this->testAction('/searches/index', array(
			'data' => array(
				'Search' => array(
					'query' => 'a'
				)
			)
		));

		$results = Set::extract('/User/username', $vars['users']);
		$expected = array(
			'jharris',
			'rickyrockharbor',
			'rickyrockharborjr'
		);
		$this->assertEqual($results, $expected);

		$results = Set::extract('/Ministry/name', $vars['ministries']);
		$expected = array(
			'Communications',
			'Alpha',
			'All Church'
		);
		$this->assertEqual($results, $expected);

		$results = Set::extract('/Involvement/name', $vars['involvements']);
		$expected = array(
			'CORE 2.0 testing',
			'Third Wednesday',
			'Team CORE'
		);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/searches/index/model:User', array(
			'data' => array(
				'Search' => array(
					'query' => 'a'
				)
			)
		));
		$results = Set::extract('/User/username', $vars['users']);
		$expected = array(
			'jharris',
			'rickyrockharbor',
			'rickyrockharborjr'
		);
		$this->assertEqual($results, $expected);

		$this->assertEqual($vars['ministries'], array());
		
		$this->assertEqual($vars['involvements'], array());
	}

	function testInvolvement() {
		$search = array(
			'Search' => array(
				'operator' => 'AND'
			),
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
			'Search' => array(
				'operator' => 'OR'
			),
			'Involvement' => array(
				'name' => 'climbing',
				'description' => 'core'
			)
		);
		$vars = $this->testAction('/searches/involvement', array(
			'data' => $search
		));
		$results = Set::extract('/Involvement/name', $vars['results']);
		$expected = array(
			'Team CORE',
			'Rock Climbing'
		);
		$this->assertEqual($results, $expected);
	}

	function testMinistry() {
		$search = array(
			'Search' => array(
				'operator' => 'AND'
			),
			'Ministry' => array(
				'name' => 'web'
			)
		);
		$vars = $this->testAction('/searches/ministry', array(
			'data' => $search
		));
		$results = Set::extract('/Ministry/name', $vars['results']);
		$expected = array(
			'Web'
		);
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
			'rickyrockharbor',
			'rickyrockharborjr'
		);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/searches/user/page:2', array(
			'data' => $search
		));
		$this->assertEqual($this->Searches->viewPath, 'elements');
	}

}
?>