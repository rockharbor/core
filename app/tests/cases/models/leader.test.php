<?php
/* Leader Test cases generated on: 2010-07-02 10:07:10 : 1278093130 */
App::import('Model', 'Leader');

class LeaderTestCase extends CakeTestCase {
	var $fixtures = array('app.ministries_rev', 'app.involvements_rev', 'app.leader', 'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.roster', 'app.role', 'app.answer', 'app.payment', 'app.payment_type', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member', 'app.household', 'app.publication', 'app.publications_user', 'app.log');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */

	var $autoFixtures = false;

	function startTest() {
		$this->loadFixtures('Leader', 'Involvement', 'Ministry', 'Campus', 'User', 'Profile');
		$this->Leader =& ClassRegistry::init('Leader');
	}

	function endTest() {
		unset($this->Leader);
		ClassRegistry::flush();
	}

	function testGetManagers() {
		$results = $this->Leader->getManagers('Involvement', 1);
		$expected = array(
			0 => array(
				'Leader' => array(
					'id' => 1,
					'user_id' => 1,
					'model' => 'Ministry',
					'model_id' => 4,
					'created' => '2010-03-30 14:09:19',
					'modified' => '2010-03-30 14:09:19'
				)
			),
			1 => array(
				'Leader' => array(
					'id' => 4,
					'user_id' => 2,
					'model' => 'Ministry',
					'model_id' => 4,
					'created' => '2010-03-30 14:09:19',
					'modified' => '2010-03-30 14:09:19'
				)
			)
		);
		$results = Set::extract('/Leader', $results);
		$this->assertEqual($results, $expected);

		$results = $this->Leader->getManagers('Ministry', 1);
		$expected = array(
			0 => array(
				'Leader' => array(
					'id' => 3,
					'user_id' => 1,
					'model' => 'Campus',
					'model_id' => 1,
					'created' => '2010-06-04 10:14:00',
					'modified' => '2010-06-04 10:14:00'
				)
			)
		);
		$results = Set::extract('/Leader', $results);
		$this->assertEqual($results, $expected);

		$this->assertFalse($this->Leader->getManagers('Campus', 1));
		$this->assertFalse($this->Leader->getManagers('Date', 1));
		$this->assertFalse($this->Leader->getManagers('Involvement', 20));
	}

}
?>