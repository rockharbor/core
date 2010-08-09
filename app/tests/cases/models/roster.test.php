<?php
/* Roster Test cases generated on: 2010-07-26 14:07:11 : 1280180951 */
App::import('Model', 'Roster');

class RosterTestCase extends CakeTestCase {
	var $fixtures = array('app.roster', 'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.leader', 'app.role', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member', 'app.household', 'app.payment', 'app.payment_type', 'app.publication', 'app.publications_user', 'app.answer', 'app.log', 'app.ministries_rev', 'app.involvements_rev');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		$this->loadFixtures('Roster', 'Payment', 'PaymentOption');
		$this->Roster =& ClassRegistry::init('Roster');
	}

	function endTest() {
		unset($this->Roster);		
		ClassRegistry::flush();
	}

	function testVirtualFields() {
		$roster = $this->Roster->read(null, 6);

		$result = $roster['Roster']['amount_paid'];
		$this->assertEqual($result, 20);

		$result = $roster['Roster']['amount_due'];
		$this->assertEqual($result, 100);

		$result = $roster['Roster']['balance'];
		$this->assertEqual($result, 80);
	}

}
?>