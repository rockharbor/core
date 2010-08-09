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

}
?>