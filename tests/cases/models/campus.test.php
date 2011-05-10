<?php
/* Campus Test cases generated on: 2010-06-30 10:06:12 : 1277919132 */
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Campus');

class CampusTestCase extends CoreTestCase {
	function startTest() {
		$this->Campus =& ClassRegistry::init('Campus');
		$this->loadFixtures('Campus', 'Leader');
	}

	function endTest() {
		unset($this->Campus);
		ClassRegistry::flush();
	}

	function testIsManager() {
		$this->assertTrue($this->Campus->isManager(1,1));
		$this->assertFalse($this->Campus->isManager(1,2));
		$this->assertFalse($this->Campus->isManager());
		$this->assertFalse($this->Campus->isManager(1));
		$this->assertFalse($this->Campus->isManager(2,1));
	}

	function testGetInvolved() {
		$this->loadFixtures('Ministry', 'Involvement', 'Roster');

		$results = $this->Campus->getInvolved(2);
		$this->assertEqual($results, array());

		$results = $this->Campus->getInvolved(1);
		sort($results);
		$this->assertEqual($results, array(1, 5));

		$results = $this->Campus->getInvolved(1, true);
		sort($results);
		$this->assertEqual($results, array(1, 2, 3, 5));
	}

	function testGetLeaders() {
		$this->loadFixtures('Ministry', 'Involvement', 'Leader');

		$results = $this->Campus->getLeaders(1);
		$this->assertEqual($results, array());

		$results = $this->Campus->getLeaders(1, true);
		sort($results);
		$this->assertEqual($results, array(1));
	}

}
?>