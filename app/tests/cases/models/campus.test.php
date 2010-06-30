<?php
/* Campus Test cases generated on: 2010-06-30 10:06:12 : 1277919132 */
App::import('Model', 'Campus');

class CampusTestCase extends CakeTestCase {
	var $fixtures = array('app.log', 'app.campus', 'plugin.media.attachment', 'app.ministry',
		'app.group', 'app.user', 'app.profile', 'app.classification',
		'app.job_category', 'app.school', 'app.comment', 'app.comment_type',
		'app.comments', 'app.notification', 'plugin.media.document', 'app.roster',
		'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode',
		'app.region', 'app.date', 'app.payment_option', 'app.question',
		'app.leader', 'app.role', 'app.roster_status', 'app.answer', 'app.payment',
		'app.payment_type', 'app.household_member', 'app.household',
		'app.publication', 'app.publications_user');

	function startTest() {
		$this->Campus =& ClassRegistry::init('Campus');
	}

	function endTest() {
		unset($this->Campus);
		ClassRegistry::flush();
	}

	function testIsManager() {
		$this->assertTrue($this->Campus->isManager(1,1));
		$this->assertFalse($this->Campus->isManager(1,2));
		$this->assertFalse($this->Campus->isManager());
		$this->assertFalse($this->Campus->isManager(1));
		$this->assertFalse($this->Campus->isManager(2,1));
	}

}
?>