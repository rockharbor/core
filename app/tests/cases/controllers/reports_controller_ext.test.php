<?php
/* Reports Test cases generated on: 2010-07-19 12:07:49 : 1279566109 */
App::import('Controller', 'Reports');

class ExtTestReportsController extends ReportsController {
	var $components = array(
		'DebugKit.Toolbar' => array(
			'autoRun' => false
		),
		'Referee.Whistle' => array(
			'enabled' => false
		),
		'QueueEmail' => array(
			'enabled' => false
		),
		'MultiSelect.MultiSelect'
	);

	function redirect($url, $status = null, $exit = true) {
		if (!$this->Session->check('TestCase.redirectUrl')) {
			$this->Session->write('TestCase.flash', $this->Session->read('Message.flash'));
			$this->Session->write('TestCase.redirectUrl', $url);
		}
	}

	function _stop($status = 0) {
		$this->Session->write('TestCase.stopped', $status);
	}

	function isAuthorized() {
		$action = str_replace('controllers/Test', '', $this->Auth->action());
		$auth = parent::isAuthorized($action);
		$this->Session->write('TestCase.authorized', $auth);
		return $auth;
	}

	function beforeRender() {
		parent::beforeFilter();
		$this->Session->write('TestCase.headers', $this->RequestHandler->responseType());
	}
}

class ReportsControllerTestCase extends CakeTestCase {
	var $fixtures = array('app.notification', 'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.roster', 'app.role', 'app.roster_status', 'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member', 'app.household', 'app.publication', 'app.publications_user', 'app.log', 'app.app_setting', 'app.alert', 'app.alerts_user', 'app.aro', 'app.aco', 'app.aros_aco', 'app.ministries_rev', 'app.involvements_rev', 'app.error', 'app.log');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		$this->Reports =& new ExtTestReportsController();
		$this->Reports->constructClasses();
		// necessary fixtures
		$this->loadFixtures('Aco', 'Aro', 'ArosAco', 'Group', 'Error');
		$this->loadFixtures('User', 'Roster', 'Ministry', 'Involvement', 'Campus');
		$this->Reports->Component->initialize($this->Reports);
		$this->Reports->Session->write('Auth.User', array('id' => 1));
		$this->Reports->Session->write('User', array('Group' => array('id' => 1)));
	}

	function endTest() {
		$this->Reports->Session->destroy();
		unset($this->Reports);		
		ClassRegistry::flush();
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
		$vars = $this->testAction('/ext_test_reports/export/Ministry/testExportPrint.print', array(
			'return' => 'vars',
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

		$results = $this->Reports->Session->read('TestCase.headers');
		$expected = 'html';
		$this->assertEqual($results, $expected);
	}

}
?>