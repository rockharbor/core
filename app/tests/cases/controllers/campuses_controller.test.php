<?php
/* Campuses Test cases generated on: 2010-07-09 14:07:25 : 1278710485 */
App::import('Controller', 'Campuses');

class TestCampusesController extends CampusesController {
	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}

	function _stop($status = 0) {
		$this->stopped = $status;
	}
}

class CampusesControllerTestCase extends CakeTestCase {
	var $fixtures = array('app.campus', 'plugin.media.attachment', 'app.ministry', 'app.group', 'app.user', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'plugin.media.document', 'app.roster', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.leader', 'app.role', 'app.roster_status', 'app.answer', 'app.payment', 'app.payment_type', 'app.household_member', 'app.household', 'app.publication', 'app.publications_user', 'app.log', 'app.notification', 'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.roster', 'app.role', 'app.roster_status', 'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member', 'app.household', 'app.publication', 'app.publications_user', 'app.log', 'app.app_setting', 'app.alert', 'app.alerts_user');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		$this->loadFixtures('Campus', 'Ministry', 'Involvement');
		$this->Campuses =& new TestCampusesController();
		$this->Campuses->constructClasses();
		$this->Campuses->Component->initialize($this->Campuses);
		$this->Campuses->Session->write('Auth.User', array('id' => 1));
		$this->Campuses->Session->write('User', array('Group' => array('id' => 1)));
	}

	function endTest() {
		$this->Campuses->Session->destroy();
		unset($this->Campuses);		
		ClassRegistry::flush();
	}

	function testView() {
		$vars = $this->testAction('/test_campuses/view/Campus:1', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Campus', $vars['campus']);
		$expected = array(
			array(
				'Campus' => array(
					'id' => 1,
					'name' => 'RH Central',
					'description' => 'The original campus!',
					'active' => 1,
					'created' => '2010-02-08 14:39:06',
					'modified' => '2010-03-11 13:34:41'
				)
			)
		);
		$this->assertEqual($results, $expected);
	}

	function testAdd() {
		$data = array(
			'name' => 'New Campus',
			'description' => 'A slightly newer campus',
			'active' => 1
		);
		$this->testAction('/test_campuses/add', array(
			'data' => $data
		));
		$count = $this->Campuses->Campus->find('count');
		$this->assertEqual($count, 3);
	}

	function testEdit() {
		$data = array(
			'id' => 1,
			'name' => 'New name'
		);
		$this->testAction('/test_campuses/edit/1', array(
			'data' => $data
		));
		$this->Campuses->Campus->id = 1;
		$this->assertEqual($this->Campuses->Campus->field('name'), 'New name');
		$this->assertNotEqual($this->Campuses->Campus->field('modified'), '2010-03-11 13:34:41');
	}

	function testDelete() {
		$this->testAction('/test_campuses/delete/1');
		$this->assertFalse($this->Campuses->Campus->read(null, 1));
		$this->assertFalse($this->Campuses->Campus->Ministry->read(null, 1));
		$this->assertFalse($this->Campuses->Campus->Ministry->Involvement->read(null, 4));
	}

}
?>