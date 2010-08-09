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
	}

}
?>