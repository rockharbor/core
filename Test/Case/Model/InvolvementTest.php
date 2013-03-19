<?php
/* Involvement Test cases generated on: 2010-07-02 10:07:50 : 1278092570 */
App::uses('CoreTestCase', 'Lib');
App::uses('Involvement', 'Model');

class InvolvementTestCase extends CoreTestCase {

	public function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Involvement', 'Leader', 'Date');
		$this->Involvement =& ClassRegistry::init('Involvement');
	}

	public function endTest() {
		unset($this->Involvement);
		ClassRegistry::flush();
	}

	public function testVirtualFields() {
		$involvement = $this->Involvement->read(null, 1);
		$this->assertTrue($involvement['Involvement']['previous']);

		$involvement = $this->Involvement->read(null, 2);
		$this->assertFalse($involvement['Involvement']['previous']);

		$involvement = $this->Involvement->read(null, 3);
		$this->assertTrue($involvement['Involvement']['previous']);
		$this->assertTrue($involvement['Involvement']['has_dates']);

		// involvements with no dates aren't considered 'previous'
		$this->Involvement->Date->deleteAll(array(
			'Date.involvement_id' => 2
		));
		$involvement = $this->Involvement->read(null, 2);
		$this->assertFalse($involvement['Involvement']['previous']);
		$this->assertFalse($involvement['Involvement']['has_dates']);
	}

	public function testIsLeader() {
		$this->assertTrue($this->Involvement->isLeader(1, 1));
		$this->assertFalse($this->Involvement->isLeader(1, 4));
		$this->assertFalse($this->Involvement->isLeader());
		$this->assertFalse($this->Involvement->isLeader(20));
		$this->assertFalse($this->Involvement->isLeader(1, 90));
	}

	public function testGetInvolved() {
		$this->loadFixtures('Roster');

		$results = $this->Involvement->getInvolved(1);
		sort($results);
		$this->assertEqual($results, array(2,3));

		$results = $this->Involvement->getInvolved(10);
		$this->assertEqual($results, array());

		$results = $this->Involvement->getInvolved(5);
		$this->assertEqual($results, array(5));
	}

	public function testGetLeaders() {
		$this->loadFixtures('Leader');

		$results = $this->Involvement->getLeaders(1);
		$this->assertEqual($results, array(1));

		$results = $this->Involvement->getLeaders(10);
		$this->assertEqual($results, array());
	}

}
