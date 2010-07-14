<?php
/* Group Test cases generated on: 2010-07-13 09:07:53 : 1279039973 */
App::import('Model', 'Group');

class GroupTestCase extends CakeTestCase {
	var $fixtures = array('app.ministries_rev', 'app.involvements_rev', 'app.group', 'app.user', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.roster', 'app.role', 'app.roster_status', 'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member', 'app.household', 'app.publication', 'app.publications_user', 'app.log');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		$this->loadFixtures('Group');
		$this->Group =& ClassRegistry::init('Group');
	}

	function endTest() {
		unset($this->Group);		
		ClassRegistry::flush();
	}

	function testFindGroups() {
		$results = $this->Group->findGroups(7);
		$expected = array(
			7 => 'Developer',
			8 => 'User'
		);
		$this->assertEqual($results, $expected);
		
		$results = Set::extract('/Group/id', $this->Group->findGroups(2, 'all', '>'));
		$expected = array(1);
		$this->assertEqual($results, $expected);

		$results = $this->Group->findGroups(4, 'list', '>=');
		$expected = array(
			1 => 'Super Administrator',
			2 => 'Administrator',
			3 => 'Pastor',
			4 => 'Communications Admin'
		);
		$this->assertEqual($results, $expected);

		$results = Set::extract('/Group/id', $this->Group->findGroups(5, 'all', '<='));
		$expected = array(5, 6, 7, 8);
		$this->assertEqual($results, $expected);
	}

}
?>