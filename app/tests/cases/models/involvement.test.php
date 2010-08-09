<?php
/* Involvement Test cases generated on: 2010-07-02 10:07:50 : 1278092570 */
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Involvement');

class InvolvementTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('Involvement', 'Leader');
		$this->Involvement =& ClassRegistry::init('Involvement');
	}

	function endTest() {
		unset($this->Involvement);
		ClassRegistry::flush();
	}

	function testIsLeader() {
		$this->assertTrue($this->Involvement->isLeader(1, 1));
		$this->assertFalse($this->Involvement->isLeader(1, 4));
		$this->assertFalse($this->Involvement->isLeader());
		$this->assertFalse($this->Involvement->isLeader(20));
		$this->assertFalse($this->Involvement->isLeader(1, 90));
	}

}
?>