<?php
/* Reports Test cases generated on: 2010-07-19 12:07:49 : 1279566109 */
App::import('Controller', 'Reports');

class TestReportsController extends ReportsController {
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
		$this->Reports =& new TestReportsController();
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

	function testMinistry() {
		$vars = $this->testAction('/test_reports/ministry', array(
			'return' => 'vars'
		));
		$results = count($vars['ministries']);
		$this->assertEqual($results, 4);

		$vars = $this->testAction('/test_reports/ministry', array(
			'return' => 'vars',
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

		$vars = $this->testAction('/test_reports/map/testMap', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Profile/name', $vars['results']);
		$expected = array('Jeremy Harris');
		$this->assertEqual($results, $expected);
	}

}
?>