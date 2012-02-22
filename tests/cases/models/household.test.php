<?php
/* Household Test cases generated on: 2010-06-29 11:06:11 : 1277837891 */
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Household');

class HouseholdTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('Household', 'HouseholdMember', 'User', 'Profile');
		$this->Household =& ClassRegistry::init('Household');
	}

	function endTest() {
		unset($this->Household);
		ClassRegistry::flush();
	}

	function testGetMemberIds() {
		$results = $this->Household->getMemberIds(1);
		sort($results);
		$expected = array(3, 100);
		$this->assertEqual($results, $expected);

		$results = $this->Household->getMemberIds(1, true);
		sort($results);
		$expected = array(3, 100);
		$this->assertEqual($results, $expected);

		$results = $this->Household->getMemberIds(3);
		sort($results);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);

		$results = $this->Household->getMemberIds(3, true);
		sort($results);
		$expected = array();
		$this->assertEqual($results, $expected);

		$results = $this->Household->getMemberIds(6);
		sort($results);
		$expected = array();
		$this->assertEqual($results, $expected);
	}

	function testGetHouseholdIds() {
		$results = $this->Household->getHouseholdIds(3);
		sort($results);
		$expected = array(1, 2, 3);
		$this->assertEqual($results, $expected);

		$results = $this->Household->getHouseholdIds(3, true);
		sort($results);
		$expected = array(3);
		$this->assertEqual($results, $expected);
	}

	function testIsMember() {
		$this->assertTrue($this->Household->isMember(2, 2));
		$this->assertTrue($this->Household->isMember(3, 2));
		$this->assertFalse($this->Household->isMember(1, 2));
		$this->assertTrue($this->Household->isMember(1, 1));
	}

	function testIsMemberWith() {
		$this->assertTrue($this->Household->isMemberWith(3, 2));
		$this->assertTrue($this->Household->isMemberWith(2, 3));
		$this->assertTrue($this->Household->isMemberWith(2, 3, 2));
		$this->assertFalse($this->Household->isMemberWith(3, 2, 1));
		$this->assertTrue($this->Household->isMemberWith(3, 2, array(1,2)));
		$this->assertFalse($this->Household->isMemberWith(1, 2, array(1,2)));
	}

	function testIsContact() {
		$this->assertTrue($this->Household->isContact(1, 1));
		$this->assertTrue($this->Household->isContact(2, 2));
		$this->assertFalse($this->Household->isContact(3, 2));
		$this->assertFalse($this->Household->isContact(2, 1));
	}

	function testIsContactFor() {
		$this->assertTrue($this->Household->isContactFor(2,3));
		$this->assertFalse($this->Household->isContactFor(1,2));
		$this->assertTrue($this->Household->isContactFor(1,3));
		$this->assertFalse($this->Household->isContactFor(1,6));
	}

	function testCreateHousehold() {
		$this->assertTrue($this->Household->createHousehold(1));
		$members = $this->Household->HouseholdMember->find('all', array(
			'conditions' => array(
				'user_id' => 1
			)
		));
		$this->assertEqual(count($members), 1);
	}

	function testMakeHouseholdContact() {
		$this->assertFalse($this->Household->makeHouseholdContact(1,2));
		$this->assertTrue($this->Household->makeHouseholdContact(3,2));
	}

	function testJoin() {
		$this->assertTrue($this->Household->join(2,1));
		$this->assertTrue($this->Household->join(2,1));
		$this->assertTrue($this->Household->join(2,1,false));
		$this->assertTrue($this->Household->join(2,1,true));
		$this->assertFalse($this->Household->join(2,10));
		$this->assertFalse($this->Household->join(20,1));
		$members = $this->Household->HouseholdMember->find('all', array(
			'conditions' => array(
				'user_id' => 1
			)
		));
		$this->assertEqual(count($members), 3);
	}

}
?>