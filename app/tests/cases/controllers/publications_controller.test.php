<?php
/* Publications Test cases generated on: 2010-07-19 10:07:29 : 1279558889 */
App::import('Controller', 'Publications');

class TestPublicationsController extends PublicationsController {
	var $components = array(
		'DebugKit.Toolbar' => array(
			'autoRun' => false
		),
		'Referee.Whistle' => array(
			'enabled' => false
		),
		'QueueEmail' => array(
			'enabled' => false
		)
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
}

class PublicationsControllerTestCase extends CakeTestCase {
	var $fixtures = array('app.notification', 'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.roster', 'app.role', 'app.roster_status', 'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member', 'app.household', 'app.publication', 'app.publications_user', 'app.log', 'app.app_setting', 'app.alert', 'app.alerts_user', 'app.aro', 'app.aco', 'app.aros_aco', 'app.ministries_rev', 'app.involvements_rev', 'app.error', 'app.log');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		$this->Publications =& new TestPublicationsController();
		$this->Publications->constructClasses();
		// necessary fixtures
		$this->loadFixtures('Aco', 'Aro', 'ArosAco', 'Group', 'Error');
		$this->loadFixtures('Publication', 'PublicationsUser', 'User');
		$this->Publications->Component->initialize($this->Publications);
		$this->Publications->Session->write('Auth.User', array('id' => 1));
		$this->Publications->Session->write('User', array('Group' => array('id' => 1)));
	}

	function endTest() {
		$this->Publications->Session->destroy();
		unset($this->Publications);		
		ClassRegistry::flush();
	}

	function testSubscription() {
		$vars = $this->testAction('/test_publications/subscriptions/User:1', array(
			'return' => 'vars'
		));
		$results = sort($vars['subscriptions']);
		$expected = array(1,2);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/test_publications/subscriptions/User:1', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Publication/name', $vars['publications']);
		$expected = array('ebulletin', 'Family Ministry Update');
		$this->assertEqual($results, $expected);
	}

	function testToggleSubscribe() {
		$this->testAction('/test_publications/toggle_subscribe/1/0/User:1');
		$vars = $this->testAction('/test_publications/subscriptions/User:1', array(
			'return' => 'vars'
		));
		$results = sort($vars['subscriptions']);
		$expected = array(2);
		$this->assertEqual($results, $expected);

		$this->testAction('/test_publications/toggle_subscribe/1/1/User:1');
		$vars = $this->testAction('/test_publications/subscriptions/User:1', array(
			'return' => 'vars'
		));
		$results = sort($vars['subscriptions']);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);

		// try duplicating subscription
		$this->testAction('/test_publications/toggle_subscribe/1/1/User:1');
		$vars = $this->testAction('/test_publications/subscriptions/User:1', array(
			'return' => 'vars'
		));
		$results = sort($vars['subscriptions']);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);

		$this->testAction('/test_publications/toggle_subscribe/1/1/User:2');
		$vars = $this->testAction('/test_publications/subscriptions/User:2', array(
			'return' => 'vars'
		));
		$results = sort($vars['subscriptions']);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);
	}

}
?>