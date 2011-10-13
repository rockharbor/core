<?php
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Address');

class AddressTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('Address');
		$this->Address =& ClassRegistry::init('Address');
	}

	function endTest() {
		unset($this->Comment);
		ClassRegistry::flush();
	}
	
	function testBeforeSave() {
		$this->Address->save(array(
			'Address' => array(
				'city' => 'Placentia',
				'zip' => '92870'
			)
		));
		$result = $this->Address->read();
		$this->assertEqual($result['Address']['name'], 'Placentia Address');
		
		$this->Address->create();
		$this->Address->save(array(
			'Address' => array(
				'zip' => '92870'
			)
		));
		$result = $this->Address->read();
		$this->assertEqual($result['Address']['name'], '92870 Address');
		
		$this->Address->create();
		$this->Address->save(array(
			'city' => 'Placentia',
			'zip' => '92870'
		));
		$result = $this->Address->read();
		$this->assertEqual($result['Address']['name'], 'Placentia Address');
	}

	function testDistance() {
		$this->assertNull($this->Address->distance());
		$this->assertNull($this->Address->distance('123'));
		$result = $this->Address->distance('1', '2');
		$this->assertIsA($result, 'string');
	}

	function testRelated() {
		$results = $this->Address->related(1);
		$this->assertEqual($results, array(2));
		$results = $this->Address->related(100);
		$this->assertFalse($results);
	}

	function testToggleActivity() {
		$result = $this->Address->toggleActivity(3, false);
		$this->assertTrue($result);
		$result = $this->Address->toggleActivity(1, false);
		$this->assertTrue($result);
		$result = $this->Address->toggleActivity(2, false);
		$this->assertFalse($result);
		$result = $this->Address->toggleActivity(4, false);
		$this->assertTrue($result);
		$result = $this->Address->toggleActivity(1, true);
		$this->assertTrue($result);
	}

}
?>