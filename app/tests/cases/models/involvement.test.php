<?php
/* Involvement Test cases generated on: 2010-07-02 10:07:50 : 1278092570 */
App::import('Model', 'Involvement');

class InvolvementTestCase extends CakeTestCase {
	var $fixtures = array('app.involvement', 'app.ministry', 'app.campus',
		'plugin.media.attachment', 'app.leader', 'app.user', 'app.group',
		'app.profile', 'app.classification', 'app.job_category', 'app.school',
		'app.comment', 'app.comment_type', 'app.comments', 'app.notification',
		'plugin.media.document', 'app.roster', 'app.role', 'app.payment_option',
		'app.roster_status', 'app.answer', 'app.question', 'app.payment',
		'app.payment_type', 'app.address', 'app.zipcode', 'app.region',
		'app.household_member', 'app.household', 'app.publication',
		'app.publications_user', 'app.involvement_type', 'app.date',
		'app.log');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */

	var $autoFixtures = false;

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