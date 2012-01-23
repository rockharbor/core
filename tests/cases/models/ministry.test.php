<?php
/* Ministry Test cases generated on: 2010-07-02 11:07:10 : 1278095350 */
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Ministry');

class MinistryTestCase extends CoreTestCase {
	function startTest() {
		$this->loadFixtures('Ministry', 'Leader');
		$this->Ministry =& ClassRegistry::init('Ministry');
	}

	function endTest() {
		unset($this->Ministry);
		ClassRegistry::flush();
	}

	function testIsManager() {
		$this->assertTrue($this->Ministry->isManager(1, 4));
		$this->assertTrue($this->Ministry->isManager(2, 4));
		$this->assertFalse($this->Ministry->isManager(2, 5));
		$this->assertFalse($this->Ministry->isManager(2));
		$this->assertFalse($this->Ministry->isManager(2, 90));
		$this->assertFalse($this->Ministry->isManager(90, 1));
		// is a parent ministry manager, but not a ministry manager or campus manager
		$this->assertTrue($this->Ministry->isManager(2, 6));
	}

	function testGetInvolved() {
		$this->loadFixtures('Roster', 'Involvement');

		$results = $this->Ministry->getInvolved(1);
		$this->assertEqual($results, array(5));

		$results = $this->Ministry->getInvolved(1, true);
		sort($results);
		$this->assertEqual($results, array(1, 2, 3, 5));

		$results = $this->Ministry->getInvolved(10);
		$this->assertEqual($results, array());
	}

	function testGetLeaders() {
		$this->loadFixtures('Involvement');

		$results = $this->Ministry->getLeaders(1);
		$this->assertEqual($results, array());

		$results = $this->Ministry->getLeaders(4);
		$this->assertEqual($results, array(1));

		$results = $this->Ministry->getLeaders(1, true);
		$this->assertEqual($results, array(1));
	}

}
?>