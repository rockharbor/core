<?php

App::import('Lib', 'CoreTestCase');
App::import('Component', 'Report');

class ReportTest extends CoreTestCase {
	
	function startTest() {
		$this->Report = new ReportComponent(new Controller());
	}

	function endTest() {		
		unset($this->Report);
		ClassRegistry::flush();
	}
	
	function testRecursiveFieldSearch() {
		$fields = array(
			'User' => array(
				'username' => 1,
				'Profile' => array(
					'first_name' => 1
				)
			)
		);
		$options = array(
			'User' => array(
				'Profile'
			)
		);
		$results = $this->Report->_recursiveFieldSearch($fields, $options);
		$expected = array(
			'User' => array(
				'fields' => array('username'),
				'Profile' => array(
					'fields' => array('first_name')
				)
			)
		);
		$this->assertEqual($results, $expected);
		
		$fields = array(
			'User' => array(
				'username' => 1,
				'Profile' => array(
					'first_name' => 1
				)
			)
		);
		$options = array(
			'User' => array(
				'Profile' => array(
					'Model',
					'fields' => array(
						'last_name'
					),
					'Creator'
				)
			)
		);
		$results = $this->Report->_recursiveFieldSearch($fields, $options);
		$expected = array(
			'User' => array(
				'fields' => array('username'),
				'Profile' => array(
					'Model' => array(),
					'fields' => array('last_name', 'first_name'),
					'Creator' => array()
				)
			)
		);
		$this->assertEqual($results, $expected);
	}

	function testGenerateSearchOptions() {
		$fields = array(
			'User' => array(
				'username' => 1,
				'Profile' => array(
					'first_name' => 1
				)
			)
		);
		$options = array(
			'conditions' => array(),
			'contain' => array(
				'User' => array(
					'Profile'
				)
			)
		);
		$results = $this->Report->generateSearchOptions($fields, $options);
		$expected = array(
			'conditions' => array(),
			'contain' => array(
				'User' => array(
					'fields' => array(
						'username'
					),
					'Profile' => array(
						'fields' => array(
							'first_name'
						)
					)
				)
			)
		);
		$this->assertEqual($results, $expected);
		
		$fields = array(
			'User' => array(
				'username' => 1,
				'Profile' => array(
					'first_name' => 1
				)
			)
		);
		$options = array(
			'conditions' => array(),
			'contain' => array(
				'User' => array(
					'Profile' => array(
						'fields' => array(
							'last_name', 'birth_date'
						)
					)
				)
			)
		);
		$results = $this->Report->generateSearchOptions($fields, $options);
		$expected = array(
			'conditions' => array(),
			'contain' => array(
				'User' => array(
					'fields' => array(
						'username'
					),
					'Profile' => array(
						'fields' => array(
							'last_name', 'birth_date', 'first_name'
						)
					)
				)
			)
		);
		$this->assertEqual($results, $expected);
		
		$fields = array(
			'User' => array(
				'Profile' => array(
					'first_name' => 1
				)
			)
		);
		$options = array(
			'conditions' => array(),
			'contain' => array(
				'User' => array(
					'Profile'
				)
			)
		);
		$results = $this->Report->generateSearchOptions($fields, $options);
		$expected = array(
			'conditions' => array(),
			'contain' => array(
				'User' => array(
					'fields' => array(),
					'Profile' => array(
						'fields' => array(
							'first_name'
						)
					)
				)
			)
		);
		$this->assertEqual($results, $expected);
	}
	
}