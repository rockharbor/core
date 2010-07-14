<?php
/* Dates Test cases generated on: 2010-07-12 09:07:14 : 1278951854 */
App::import('Controller', 'Dates');

class TestDatesController extends DatesController {
	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}

	function _stop($status = 0) {
		$this->stopped = $status;
	}
}

class DatesControllerTestCase extends CakeTestCase {
	var $fixtures = array('app.ministries_rev', 'app.involvements_rev','app.notification', 'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.roster', 'app.role', 'app.roster_status', 'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member', 'app.household', 'app.publication', 'app.publications_user', 'app.log', 'app.app_setting', 'app.alert', 'app.alerts_user', 'app.log');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		$this->loadFixtures('Involvement', 'Date');
		$this->Dates =& new TestDatesController();
		$this->Dates->constructClasses();
		$this->Dates->Component->initialize($this->Dates);
		$this->Dates->Session->write('Auth.User', array('id' => 1));
		$this->Dates->Session->write('User', array('Group' => array('id' => 1)));
	}

	function endTest() {
		$this->Dates->Session->destroy();
		unset($this->Dates);		
		ClassRegistry::flush();
	}

	function testCalendar() {
		$vars = $this->testAction('/test_dates/calendar.json', array(
			'return' => 'vars',
			'method' => 'get',
			'data' => array(
				'start' => strtotime('5/1/2010'),
				'end' => strtotime('5/30/2010')
			)
		));
		$results = Set::extract('/Involvement', $vars['events']);
		$expected = array(
			array(
				'Involvement' => array(
					'id' => 2,
					'name' => 'Third Wednesday'					
				)
			)
		);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/test_dates/calendar/passed.json', array(
			'return' => 'vars',
			'method' => 'get',
			'data' => array(
				'start' => strtotime('4/1/2010'),
				'end' => strtotime('4/30/2010')
			)
		));
		$results = Set::extract('/Involvement', $vars['events']);
		$expected = array(
			array(
				'Involvement' => array(
					'id' => 1,
					'name' => 'CORE 2.0 testing'
				)
			),
			array(
				'Involvement' => array(
					'id' => 2,
					'name' => 'Third Wednesday'
				)
			),
			array(
				'Involvement' => array(
					'id' => 3,
					'name' => 'Team CORE'
				)
			)			
		);
		$this->assertEqual($results, $expected);
	}

	function testIndex() {
		$vars = $this->testAction('/test_dates/index/Involvement:2', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Date', $vars['dates']);
		$expected = array(
			array(
				'Date' => array(
					'id' => 1,
					'start_date' => '2010-03-16',
					'end_date' => '2010-03-16',
					'start_time' => '00:00:00',
					'end_time' => '11:59:00',
					'all_day' => 1,
					'permanent' => 1,
					'recurring' => 1,
					'recurrance_type' => 'mw',
					'frequency' => 1,
					'weekday' => 3,
					'day' => 1,
					'involvement_id' => 2,
					'created' => '2010-03-16 13:32:33',
					'modified' => '2010-03-16 13:32:48',
					'exemption' => 0,
					'offset' => 3,
					'passed' => 0
				)
			)
		);
		$this->assertEqual($results, $expected);
	}

	function testAdd() {
		$data = array(
			'Date' => array(
				'start_date' => '2010-03-16',
				'end_date' => '2010-03-16',
				'start_time' => '07:05:00',
				'end_time' => '10:00:00',
				'all_day' => 1,
				'permanent' => 0,
				'recurring' => 0,
				'recurrance_type' => 'mw',
				'frequency' => 1,
				'weekday' => 0,
				'day' => 0,
				'involvement_id' => 2,
				'exemption' => 1,
				'offset' => 0
			)
		);
		$this->testAction('/test_dates/add', array(
			'data' => $data
		));
		$this->Dates->Date->id = 7;
		$date = $this->Dates->Date->read();
		$this->assertEqual($date['Date']['start_time'], '00:00:00');
		$this->assertEqual($date['Date']['end_time'], '23:59:00');
	}

	function testEdit() {
		$data = array(
			'Date' => array(
				'id' => 1,
				'end_date' => '2010-03-18',
				'permanent' => 0
			)
		);
		$this->testAction('/test_dates/edit/1', array(
			'data' => $data
		));
		$this->Dates->Date->id = 1;
		$date = $this->Dates->Date->read();
		$this->assertNotEqual($date['Date']['modified'], '2010-03-16 13:32:48');
	}

	function testDelete() {
		$this->testAction('/test_dates/delete/1');
		$result = $this->Dates->Date->read(null, 1);
		$this->assertFalse($result);
	}

}
?>