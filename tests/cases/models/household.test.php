<?php
/* Household Test cases generated on: 2010-06-29 11:06:11 : 1277837891 */
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Household');

class HouseholdTestCase extends CoreTestCase {

	public function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Household', 'HouseholdMember', 'User', 'Profile');
		$this->Household =& ClassRegistry::init('Household');
	}

	public function endTest() {
		unset($this->Household);
		ClassRegistry::flush();
	}

	public function testGetMemberIds() {
		$results = $this->Household->getMemberIds(1);
		sort($results);
		$expected = array(3, 5, 6, 100);
		$this->assertEqual($results, $expected);

		$results = $this->Household->getMemberIds(1, true);
		sort($results);
		$expected = array(3, 5, 6, 100);
		$this->assertEqual($results, $expected);

		$results = $this->Household->getMemberIds(3);
		sort($results);
		$expected = array(1, 2, 5, 6, 97, 98, 99);
		$this->assertEqual($results, $expected);

		$results = $this->Household->getMemberIds(3, true);
		sort($results);
		$expected = array(5, 97);
		$this->assertEqual($results, $expected);

		$results = $this->Household->getMemberIds(6);
		sort($results);
		$expected = array(1, 3, 5);
		$this->assertEqual($results, $expected);

		$results = $this->Household->getMemberIds(2, false);
		sort($results);
		$expected = array(3, 98, 99);
		$this->assertEqual($results, $expected);

		$results = $this->Household->getMemberIds(2, false, true);
		sort($results);
		$expected = array(3);
		$this->assertEqual($results, $expected);
	}

	public function testGetHouseholdIds() {
		$results = $this->Household->getHouseholdIds(3);
		sort($results);
		$expected = array(1, 2, 3);
		$this->assertEqual($results, $expected);

		$results = $this->Household->getHouseholdIds(3, true);
		sort($results);
		$expected = array(3);
		$this->assertEqual($results, $expected);
	}

	public function testIsMember() {
		$this->assertTrue($this->Household->isMember(2, 2));
		$this->assertTrue($this->Household->isMember(3, 2));
		$this->assertFalse($this->Household->isMember(1, 2));
		$this->assertTrue($this->Household->isMember(1, 1));
	}

	public function testIsMemberWith() {
		$this->assertTrue($this->Household->isMemberWith(3, 2));
		$this->assertTrue($this->Household->isMemberWith(2, 3));
		$this->assertTrue($this->Household->isMemberWith(2, 3, 2));
		$this->assertFalse($this->Household->isMemberWith(3, 2, 1));
		$this->assertTrue($this->Household->isMemberWith(3, 2, array(1,2)));
		$this->assertFalse($this->Household->isMemberWith(1, 2, array(1,2)));
	}

	public function testIsContact() {
		$this->assertTrue($this->Household->isContact(1, 1));
		$this->assertTrue($this->Household->isContact(2, 2));
		$this->assertFalse($this->Household->isContact(3, 2));
		$this->assertFalse($this->Household->isContact(2, 1));
	}

	public function testIsContactFor() {
		$this->assertTrue($this->Household->isContactFor(2,3));
		$this->assertFalse($this->Household->isContactFor(1,2));
		$this->assertTrue($this->Household->isContactFor(1,3));
		$this->assertTrue($this->Household->isContactFor(1,6));
	}

	public function testCreateHousehold() {
		$this->assertTrue($this->Household->createHousehold(90));
		$members = $this->Household->HouseholdMember->find('all', array(
			'conditions' => array(
				'user_id' => 90
			)
		));
		$originalHouseholds = Set::extract('/HouseholdMember/household_id', $members);
		$this->assertEqual(count($originalHouseholds), 1);

		$this->assertTrue($this->Household->createHousehold(91));

		$members = $this->Household->HouseholdMember->find('all', array(
			'conditions' => array(
				'user_id' => 90
			)
		));
		$newHouseholds = Set::extract('/HouseholdMember/household_id', $members);
		$this->assertEqual($originalHouseholds, $newHouseholds);

		$this->assertTrue($this->Household->HouseholdMember->hasAny(array('user_id' => 91)));

		// doesn't have their own confirmed household, so create one
		$origCount = $this->Household->HouseholdMember->find('count', array(
			'conditions' => array(
				'user_id' => 97
			)
		));
		$this->assertTrue($this->Household->createHousehold(97));
		$newCount = $this->Household->HouseholdMember->find('count', array(
			'conditions' => array(
				'user_id' => 97
			)
		));
		$this->assertEqual($newCount-$origCount, 1);

		// already belongs to a household where they are confirmed
		$origCount = $this->Household->HouseholdMember->find('count', array(
			'conditions' => array(
				'user_id' => 97
			)
		));
		$this->assertTrue($this->Household->createHousehold(97));
		$newCount = $this->Household->HouseholdMember->find('count', array(
			'conditions' => array(
				'user_id' => 97
			)
		));
		$this->assertEqual($newCount, $origCount);
	}

	public function testMakeHouseholdContact() {
		$this->assertFalse($this->Household->makeHouseholdContact(1,2));
		$this->assertTrue($this->Household->makeHouseholdContact(3,2));
	}

	public function testJoin() {
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
