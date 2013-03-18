<?php
/* Ministry Test cases generated on: 2010-07-02 11:07:10 : 1278095350 */
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Ministry');

class MinistryTestCase extends CoreTestCase {
	public function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Ministry', 'Leader');
		$this->Ministry =& ClassRegistry::init('Ministry');
	}

	public function endTest() {
		unset($this->Ministry);
		ClassRegistry::flush();
	}

	public function testIsManager() {
		$this->assertTrue($this->Ministry->isManager(1, 4));
		$this->assertTrue($this->Ministry->isManager(2, 4));
		$this->assertFalse($this->Ministry->isManager(2, 5));
		$this->assertFalse($this->Ministry->isManager(2));
		$this->assertFalse($this->Ministry->isManager(2, 90));
		$this->assertFalse($this->Ministry->isManager(90, 1));
		// is a parent ministry manager, but not a ministry manager or campus manager
		$this->assertTrue($this->Ministry->isManager(2, 6));
	}

	public function testGetInvolved() {
		$this->loadFixtures('Roster', 'Involvement');

		$results = $this->Ministry->getInvolved(1);
		$this->assertEqual($results, array(5));

		$results = $this->Ministry->getInvolved(1, true);
		sort($results);
		$this->assertEqual($results, array(1, 2, 3, 5, 6));

		$results = $this->Ministry->getInvolved(10);
		$this->assertEqual($results, array());
	}

	public function testGetLeaders() {
		$this->loadFixtures('Involvement');

		$results = $this->Ministry->getLeaders(1);
		$this->assertEqual($results, array());

		$results = $this->Ministry->getLeaders(4);
		$this->assertEqual($results, array(1, 2));
	}

	public function testGetLeading() {
		$results = $this->Ministry->getLeading(1);
		sort($results);
		$expected = array(4);
		$this->assertEqual($results, $expected);

		$results = $this->Ministry->getLeading(1, true);
		sort($results);
		$expected = array(4, 6);
		$this->assertEqual($results, $expected);
	}

}
