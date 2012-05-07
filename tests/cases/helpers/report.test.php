<?php
App::import('Lib', 'CoreTestCase');
App::import('Helper', array('Report'));

class ReportHelperTestCase extends CoreTestCase {
	
	var $skipSetup = true;

	function startTest($method) {
		parent::startTest($method);
		$this->Report = new ReportHelper();
	}

	function endTest() {
		unset($this->Report);
		ClassRegistry::flush();
	}
	
	function testSquash() {
		$fields = array('Address.address_line_1', 'Address.city', 'Address.state', 'Address.zip');
		$format = '%s %s, %s %d';
		$alias = 'Address';
		$this->Report->squash('Address.name', $fields, $format, $alias);
		$expected = array(
			'Address.name' => array(
				'fields' => $fields,
				'format' => $format,
				'alias' => $alias
			)
		);
		$this->assertEqual($this->Report->_squashed, $expected);
	}

	function testGetResultsWithSquashed() {
		$headers = array(
			'User' => array(
				'username' => null,
			),
			'Address' => array(
				'name' => null
			)
		);
		$squashed = array(
			'Address.name' => array(
				'fields' => array('Address.address_line_1', 'Address.city', 'Address.state', 'Address.zip'),
				'format' => '%s %s, %s %d',
				'alias' => 'Address'
			)
		);
		$data = array(
			array(
				'User' => array(
					'username' => 'jeremy',
				),
				'Address' => array(
					'name' => 'Home',
					'address_line_1' => '123 Main',
					'city' => 'Somewhere',
					'state' => 'CA',
					'zip' => 12345
				)
			),
			array(
				'User' => array(
					'username' => 'rickyrockharbor',
				),
				'Address' => array(
					'name' => 'Home',
					'address_line_1' => '456 Main',
					'city' => 'Anywhere',
					'state' => 'CA',
					'zip' => 78910
				)
			)
		);

		$this->Report->squashFields(serialize($squashed));
		
		$results  = $this->Report->createHeaders($headers);
		$expected = array(
			'Username', 'Address'
		);
		
		$this->Report->set($data);
		$results = $this->Report->getResults();
		$expected = array(
			array('jeremy', '123 Main Somewhere, CA 12345'),
			array('rickyrockharbor', '456 Main Anywhere, CA 78910'),
		);
		$this->assertEqual($results, $expected);
	}
	
	function testSquashFields() {
		$fields = array(array('Address.address_line_1', 'Address.city', 'Address.state', 'Address.zip'), '%s/n%s, %s %d', 'Address');
		$this->Report->squashFields(serialize($fields));
		$this->assertEqual($this->Report->_squashed, $fields);
	}

	function testAlias() {
		$this->Report->alias(array('Some.Field.value' => 'Readable Title'));
		$expected = array(
			'Some.Field.value' => 'Readable Title'
		);
		$this->assertEqual($this->Report->_aliases, $expected);
		
		$result = $this->Report->alias();
		$this->assertEqual($result, $expected);

		$this->Report->alias('Another', 'alias');
		$result = $this->Report->alias();
		$expected = array(
			'Some.Field.value' => 'Readable Title',
			'Another' => 'alias'
		);
		$this->assertEqual($result, $expected);
	}

	function testHeaderAliases() {
		$this->Report->alias(array('Some.Field.value' => 'Readable Title'));

		$result = $this->Report->headerAliases();
		$this->assertEqual($result, serialize($this->Report->_aliases));

		$this->Report->headerAliases($result);
		$this->assertEqual($this->Report->alias(), array('Some.Field.value' => 'Readable Title'));
	}

	function testCreateHeadersWithAliases() {
		$data = array(
			'User' => array(
				'username' => null,
				'Profile' => array(
					'name' => 0,
					'first_name' => null,
					'last_name' => null
				)
			)
		);
		$this->Report->alias(array('User.username' => 'Alias Username'));
		$this->Report->alias(array('User.Profile.first_name' => 'Whaaaaaaaaat?'));
		$results = $this->Report->createHeaders($data);
		$expected = array('Alias Username', 'Whaaaaaaaaat?', 'Last Name');
		$this->assertEqual($results, $expected);
	}

	function testNormalize() {
		$data = array(
			'User' => array(
				'username' => null,
				'Profile' => array(
					'name' => 0,
					'first_name' => null,
					'last_name' => 0
				)
			)
		);
		$result = $this->Report->normalize($data);
		$expected = array(
			'User' => array(
				'username' => null,
				'Profile' => array(
					'first_name' => null
				)
			)
		);
		$this->assertEqual($result, $expected);
	}

	function testCreateHeaders() {
		$data = array(
			'User' => array(
				'username' => null,
				'Profile' => array(
					'name' => 0,
					'first_name' => null,
					'last_name' => 0
				)
			)
		);
		$results = $this->Report->createHeaders($data);
		$expected = array('Username', 'First Name');
		$this->assertEqual($results, $expected);

		$expected = array(
			'User' => array(
				'username' => null,
				'Profile' => array(
					'first_name' => null,
				)
			)
		);
		$this->assertEqual($this->Report->_fields, $expected);
	}
	
	function testCreateHeadersWithMultipleRecords() {
		$headers = array(
			'HouseholdMember' => array(
				'Household' => array(
					'HouseholdContact' => array(
						'Profile' => array(
							'primary_email' => 1
						)
					)
				)
			)
		);
		$data = array(
			array(
				'HouseholdMember' => array(
					'Household' => array(
						array(
							'HouseholdContact' => array(
								'Profile' => array(
									'primary_email' => 'jharris@rockharbor.org'
								)
							)
						),
						array(
							'HouseholdContact' => array(
								'Profile' => array(
									'primary_email' => 'contact@example.com'
								)
							)
						)
					)
				)
			)
		);
		
		$this->Report->set($data);
		$this->Report->multiple('HouseholdMember.Household.HouseholdContact.Profile.primary_email', 'expand');
		
		$results = $this->Report->createHeaders($headers);
		$expected = array('Primary Email 1', 'Primary Email 2');
		$this->assertEqual($results, $expected);
		
		$headers = array(
			'Roster' => array(
				'RosterStatus' => array(
					'name' => 1
				)
			),
			'Answer' => array(
				'description' => 1
			)
		);
		$data = array(
			array(
				'Roster' => array(
					'RosterStatus' => array(
						'name' => 'Confirmed'
					)
				),
				'Answer' => array(
					array(
						'description' => 'I only answered one question'
					)
				)
			),
			array(
				'Roster' => array(
					'RosterStatus' => array(
						'name' => 'Pending'
					)
				),
				'Answer' => array(
					array(
						'description' => 'Answer to question 1'
					),
					array(
						'description' => 'Answer to question 2'
					)
				)
			)
		);
		
		$this->Report->reset();
		$this->Report->set($data);
		$this->Report->multiple('Answer.description', 'expand');
		$results = $this->Report->createHeaders($headers);
		$expected = array('Name', 'Description 1', 'Description 2');
		$this->assertEqual($results, $expected);
		
		$headers = array(
			'Roster' => array(
				'RosterStatus' => array(
					'name' => 1
				)
			),
			'Answer' => array(
				'description' => 1
			)
		);
		$data = array(
			array(
				'Roster' => array(
					'RosterStatus' => array(
						'name' => 'Confirmed'
					)
				),
				'Answer' => array(
					array(
						'description' => 'I only answered one question'
					)
				)
			),
			array(
				'Roster' => array(
					'RosterStatus' => array(
						'name' => 'Pending'
					)
				),
				'Answer' => array(
					array(
						'description' => 'Answer to question 1'
					),
					array(
						'description' => 'Answer to question 2'
					)
				)
			)
		);
		
		$this->Report->reset();
		$this->Report->set($data);
		$this->Report->multiple('Answer.description', 'concat');
		$results = $this->Report->createHeaders($headers);
		$expected = array('Name', 'Description');
		$this->assertEqual($results, $expected);
	}

	function testGetResults() {
		$headers = array(
			'User' => array(
				'username' => null,
				'Profile' => array(
					'name' => 0,
					'first_name' => null,
					'last_name' => 0
				)
			)
		);
		$data = array(
			array(
				'User' => array(
					'username' => 'jeremy',
					'extra' => 'field',
					'Profile' => array(
						'name' => 'Jeremy Harris',
						'first_name' => 'Jeremy',
						'last_name' => 'Harris',
						'favorite_number' => 42
					)
				),
				'Model' => array(
					'another' => 'field'
				)
			),
			array(
				'User' => array(
					'username' => 'rickyrockharbor',
					'extra' => 'field',
					'Profile' => array(
						'name' => 'Ricky Rockharbor',
						'first_name' => 'Ricky',
						'last_name' => 'Rockharbor',
						'favorite_number' => 56
					)
				),
				'Model' => array(
					'another' => 'field'
				)
			)
		);
		$this->assertEqual($this->Report->getResults(), array());
		$this->Report->set($data);
		$this->assertEqual($this->Report->getResults(), array());

		$this->Report->set($data);
		$this->Report->createHeaders($headers);
		$results = $this->Report->getResults();
		$expected = array(
			array('jeremy', 'Jeremy'),
			array('rickyrockharbor', 'Ricky'),
		);
		$this->assertEqual($results, $expected);
	}
	
	function testGetResultsWithMultipleRecords() {
		$headers = array(
			'HouseholdMember' => array(
				'Household' => array(
					'HouseholdContact' => array(
						'Profile' => array(
							'primary_email' => 1
						)
					)
				)
			)
		);
		$data = array(
			array(
				'HouseholdMember' => array(
					'Household' => array(
						array(
							'HouseholdContact' => array(
								'Profile' => array(
									'primary_email' => 'jharris@rockharbor.org'
								)
							)
						),
						array(
							'HouseholdContact' => array(
								'Profile' => array(
									'primary_email' => 'contact@example.com'
								)
							)
						)
					)
				)
			)
		);
		$this->Report->reset();
		$this->Report->set($data);
		$this->Report->multiple('HouseholdMember.Household.HouseholdContact.Profile.primary_email');
		$this->Report->createHeaders($headers);
		$results = $this->Report->getResults();
		$expected = array(
			array('jharris@rockharbor.org')
		);
		$this->assertEqual($results, $expected);
		
		$this->Report->reset();
		$this->Report->set($data);
		$this->Report->multiple('HouseholdMember.Household.HouseholdContact.Profile.primary_email', 'concat');
		$this->Report->createHeaders($headers);
		$results = $this->Report->getResults();
		$expected = array(
			array('jharris@rockharbor.org, contact@example.com')
		);
		$this->assertEqual($results, $expected);
		
		$this->Report->reset();
		$this->Report->set($data);
		$this->Report->multiple('HouseholdMember.Household.HouseholdContact.Profile.primary_email', 'expand');
		$this->Report->createHeaders($headers);
		$results = $this->Report->getResults();
		$expected = array(
			array('jharris@rockharbor.org', 'contact@example.com')
		);
		$this->assertEqual($results, $expected);
		
		$headers = array(
			'Roster' => array(
				'RosterStatus' => array(
					'name' => 1
				)
			),
			'Answer' => array(
				'description' => 1
			)
		);
		$data = array(
			array(
				'Roster' => array(
					'RosterStatus' => array(
						'name' => 'Confirmed'
					)
				),
				'Answer' => array(
					array(
						'description' => 'I only answered one question'
					)
				)
			),
			array(
				'Roster' => array(
					'RosterStatus' => array(
						'name' => 'Pending'
					)
				),
				'Answer' => array(
					array(
						'description' => 'Answer to question 1'
					),
					array(
						'description' => 'Answer to question 2'
					)
				)
			)
		);
		$this->Report->reset();
		$this->Report->set($data);
		$this->Report->multiple('Answer.description', 'expand');
		$this->Report->createHeaders($headers);
		$results = $this->Report->getResults();
		$expected = array(
			array('Confirmed', 'I only answered one question', null),
			array('Pending', 'Answer to question 1', 'Answer to question 2')
		);
		$this->assertEqual($results, $expected);
	}
	 
	function testSet() {
		$data = array(
			array(
				'User' => array(
					'username' => 'jeremy',
				),
				'Address' => array(
					'name' => 'Home',
					'address_line_1' => '123 Main',
					'city' => 'Somewhere',
					'state' => 'CA',
					'zip' => 12345
				)
			),
			array(
				'User' => array(
					'username' => 'rickyrockharbor',
				),
				'Address' => array(
					'name' => 'Home',
					'address_line_1' => '456 Main',
					'city' => 'Anywhere',
					'state' => 'CA',
					'zip' => 78910
				)
			)
		);
		
		$this->Report->set($data);
		$this->assertEqual($this->Report->data, $data);
	}

}
?>