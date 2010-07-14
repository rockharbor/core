<?php
/* Ministry Test cases generated on: 2010-07-02 11:07:10 : 1278095350 */
App::import('Model', 'Ministry');

class MinistryTestCase extends CakeTestCase {
	var $fixtures = array('app.ministries_rev', 'app.involvements_rev', 'app.ministry', 'app.campus', 'plugin.media.attachment', 'app.leader', 'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'plugin.media.document', 'app.roster', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.role', 'app.roster_status', 'app.answer', 'app.payment', 'app.payment_type', 'app.household_member', 'app.household', 'app.publication', 'app.publications_user', 'app.log');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */

	var $autoFixtures = false;

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