<?php
/* Leader Test cases generated on: 2010-07-02 10:07:10 : 1278093130 */
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Leader');

class LeaderTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('Leader', 'Involvement', 'Ministry', 'Campus', 'User', 'Profile');
		$this->Leader =& ClassRegistry::init('Leader');
	}

	function endTest() {
		unset($this->Leader);
		ClassRegistry::flush();
	}

	function testGetManagers() {
		$results = $this->Leader->getManagers('Involvement', 1);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);

		$results = $this->Leader->getManagers('Involvement', array(1, 2));
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);

		$results = $this->Leader->getManagers('Ministry', 1);
		$expected = array(1);
		$this->assertEqual($results, $expected);

		$this->assertFalse($this->Leader->getManagers('Campus', 1));
		$this->assertFalse($this->Leader->getManagers('Date', 1));
		$this->assertFalse($this->Leader->getManagers('Involvement', 20));
	}

}
?>