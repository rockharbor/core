<?php
/* Household Test cases generated on: 2010-06-29 11:06:11 : 1277837891 */
App::import('Model', 'Household');

class HouseholdTestCase extends CakeTestCase {
	var $fixtures = array(
		'app.household', 'app.user', 'app.group', 'app.profile',
		'app.classification', 'app.job_category', 'app.school', 'app.campus',
		'plugin.media.attachment', 'app.ministry', 'app.involvement',
		'app.involvement_type', 'app.address', 'app.zipcode', 'app.region',
		'app.date', 'app.payment_option', 'app.question', 'app.roster',
		'app.role', 'app.roster_status', 'app.answer', 'app.payment',
		'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type',
		'app.comments', 'app.notification', 'app.image', 'plugin.media.document',
		'app.household_member', 'app.publication', 'app.publications_user',
		'app.log', 'app.ministries_rev', 'app.involvements_rev'
	);

	var $autoFixtures = false;

	function _prepareAction($action = '') {
		$this->Household->params = Router::parse($action);
		$this->Household->passedArgs = array_merge($this->Household->params['named'], $this->Household->params['pass']);
		$this->Household->params['url'] = $this->Household->params;
		$this->Household->beforeFilter();
	}

	function startTest() {
		$this->loadFixtures('Household', 'HouseholdMember', 'User');
		$this->Household =& ClassRegistry::init('Household');
	}

	function endTest() {
		unset($this->Household);
		ClassRegistry::flush();
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
		$this->assertFalse($this->Household->isContactFor(1,3));
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
		$this->assertEqual(count($members), 2);
	}

}
?>