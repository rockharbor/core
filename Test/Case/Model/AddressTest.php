<?php
App::uses('CoreTestCase', 'Lib');
App::uses('Address', 'Model');

class AddressTestCase extends CoreTestCase {

	public function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Address');
		$this->Address =& ClassRegistry::init('Address');
	}

	public function endTest() {
		unset($this->Comment);
		ClassRegistry::flush();
	}

	public function testBeforeSave() {
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

	public function testDistance() {
		$this->assertNull($this->Address->distance());
		$this->assertNull($this->Address->distance('123'));
		$result = $this->Address->distance('1', '2');
		$this->assertIsA($result, 'string');
	}

	public function testRelated() {
		$results = $this->Address->related(1);
		$this->assertEqual($results, array(2));
		$results = $this->Address->related(100);
		$this->assertFalse($results);
	}

	public function testToggleActivity() {
		$result = $this->Address->toggleActivity(3, false);
		$this->assertTrue($result);
		$result = $this->Address->toggleActivity(1, false);
		$this->assertTrue($result);
		$result = $this->Address->toggleActivity(2, false);
		$this->assertFalse($result);
		$result = $this->Address->toggleActivity(4, false);
		$this->assertFalse($result);
		$result = $this->Address->toggleActivity(1, true);
		$this->assertTrue($result);
	}

	public function testSetPrimary() {
		$this->Address->setPrimary(1);
		$this->Address->id = 1;
		$this->assertEqual($this->Address->field('primary'), 1);
		$this->Address->id = 2;
		$this->assertEqual($this->Address->field('primary'), 0);

		$this->Address->setPrimary(2);
		$this->Address->id = 1;
		$this->assertEqual($this->Address->field('primary'), 0);
		$this->Address->id = 2;
		$this->assertEqual($this->Address->field('primary'), 1);
	}

}
