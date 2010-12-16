<?php
App::import('Helper', array('Report'));

class ReportHelperTestCase extends CakeTestCase {

	function startTest() {
		$this->Report = new ReportHelper();
	}

	function endTest() {
		unset($this->Report);
		ClassRegistry::flush();
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
		$this->assertEqual($this->Report->getResults($data), array());

		$this->Report->createHeaders($headers);
		$results = $this->Report->getResults($data);
		$expected = array(
			array('jeremy', 'Jeremy'),
			array('rickyrockharbor', 'Ricky'),
		);
		$this->assertEqual($results, $expected);
	}

}
?>