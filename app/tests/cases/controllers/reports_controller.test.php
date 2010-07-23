<?php
/* Reports Test cases generated on: 2010-07-19 12:07:49 : 1279566109 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('RequestHandler', 'QueueEmail'));
App::import('Controller', 'Reports');

Mock::generate('QueueEmailComponent');
Mock::generatePartial('RequestHandlerComponent', 'MockRequestHandlerComponent', array('_header'));
Mock::generatePartial('ReportsController', 'TestReportsController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));


class ReportsControllerTestCase extends CoreTestCase {
	var $fixtures = array('app.notification', 'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.roster', 'app.role', 'app.roster_status', 'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member', 'app.household', 'app.publication', 'app.publications_user', 'app.log', 'app.app_setting', 'app.alert', 'app.alerts_user', 'app.aro', 'app.aco', 'app.aros_aco', 'app.ministries_rev', 'app.involvements_rev', 'app.error', 'app.log');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		// necessary fixtures
		$this->loadFixtures('User', 'Roster', 'Ministry', 'Involvement', 'Campus');

		$this->Reports = new TestReportsController();
		$this->Reports->constructClasses();
		$this->Reports->Component->initialize($this->Reports);
		$this->Reports->RequestHandler = new MockRequestHandlerComponent();
		$this->Reports->QueueEmail = new MockQueueEmailComponent();

		$this->Reports->setReturnValue('isAuthorized', true);
		$this->Reports->QueueEmail->setReturnValue('send', true);
	}

	function endTest() {	
		ClassRegistry::flush();
	}

	function testMinistry() {
		$vars = $this->testAction($this->Reports, '/reports/ministry');
		$results = count($vars['ministries']);
		$this->assertEqual($results, 4);

		$vars = $this->testAction($this->Reports, '/reports/ministry', array(
			'data' => array(
				'Ministry' => array(
					'id' => 2
				)
			)
		));
		$results = Set::extract('/Ministry/name', $vars['ministries']);
		$expected = array(
			'Alpha'
		);
		$this->assertEqual($results, $expected);
	}

	function testMap() {
		$this->loadFixtures('Profile');
		
		$this->Reports->Session->write('MultiSelect.testMap', array(
			'selected' => array(1),
			'search' => array(
				'conditions' => array(
					'User.username' => 'jharris'
				)
			)
		));

		$vars = $this->testAction($this->Reports, '/reports/map/testMap');
		$results = Set::extract('/Profile/name', $vars['results']);
		$expected = array('Jeremy Harris');
		$this->assertEqual($results, $expected);
	}

	function testExportCsvWithSearch() {
		$this->Reports->Session->write('MultiSelect.testExportCsvWithSearch', array(
			'selected' => array(),
		   'search' => array(
				'conditions' => array(
					'Ministry.parent_id' => null
				)
			)
		));
		$data = array(
			'Export' => array(
				'Ministry' => array(
					'name'
				)
			)
		);
		
		$this->Reports->RequestHandler->expectAt(0, '_header', array('Content-Type: application/vnd.ms-excel; charset=UTF-8'));
		$this->Reports->RequestHandler->expectAt(1, '_header', array('Content-Disposition: attachment; filename="ministry-search-export.csv"'));
		$vars = $this->testAction($this->Reports, '/reports/export/Ministry/testExportCsvWithSearch.csv', array(
			'data' => $data
		));
		
		$results = Set::extract('/Ministry/name', $vars['results']);
		$expected = array('Communications', 'Alpha', 'All Church');
		$this->assertEqual($results, $expected);
	}

	function testExportPrint() {
		$this->Reports->Session->write('MultiSelect.testExportPrint', array(
			'selected' => array(2,3),
			'search' => array(
				'conditions' => array()
			)
		));
		$data = array(
			'Export' => array(
				'Ministry' => array(
					'name'
				)
			)
		);
		$this->Reports->RequestHandler->expectAt(0, '_header', array('Content-Type: text/html; charset=UTF-8'));
		$vars = $this->testAction($this->Reports, '/reports/export/Ministry/testExportPrint.print', array(
			'data' => $data
		));
		$results = $vars['models'];
		$expected = array(
			'Ministry' => array(
				'name'
		));
		$this->assertEqual($results, $expected);

		$results = Set::extract('/Ministry/name', $vars['results']);
		$expected = array('Alpha', 'All Church');
		$this->assertEqual($results, $expected);
	}

}
?>