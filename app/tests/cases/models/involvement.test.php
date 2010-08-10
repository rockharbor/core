<?php
/* Involvement Test cases generated on: 2010-07-02 10:07:50 : 1278092570 */
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Involvement');

class InvolvementTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('Involvement', 'Leader', 'Date');
		$this->Involvement =& ClassRegistry::init('Involvement');
	}

	function endTest() {
		unset($this->Involvement);
		ClassRegistry::flush();
	}

	function testVirtualFields() {
		$involvement = $this->Involvement->read(null, 1);
		$this->assertTrue($involvement['Involvement']['passed']);

		$involvement = $this->Involvement->read(null, 2);
		$this->assertFalse($involvement['Involvement']['passed']);

		$involvement = $this->Involvement->read(null, 3);
		$this->assertTrue($involvement['Involvement']['passed']);
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