<?php
/* Leaders Test cases generated on: 2010-07-14 12:07:47 : 1279136267 */
App::import('Controller', 'InvolvementLeaders');

class TestLeadersController extends InvolvementLeadersController {
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

class LeadersControllerTestCase extends CakeTestCase {
	var $fixtures = array('app.notification', 'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.roster', 'app.role', 'app.roster_status', 'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member', 'app.household', 'app.publication', 'app.publications_user', 'app.log', 'app.app_setting', 'app.alert', 'app.alerts_user', 'app.aro', 'app.aco', 'app.aros_aco', 'app.ministries_rev', 'app.involvements_rev', 'app.error', 'app.log');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		$this->Leaders =& new TestLeadersController();
		$this->Leaders->constructClasses();
		// necessary fixtures
		$this->loadFixtures('Aco', 'Aro', 'ArosAco', 'Group', 'Error');
		$this->loadFixtures('Leader', 'User', 'Profile', 'Involvement', 'Notification');
		$this->Leaders->Component->initialize($this->Leaders);
		$this->Leaders->Session->write('Auth.User', array('id' => 1));
		$this->Leaders->Session->write('User', array('Group' => array('id' => 1)));
	}

	function endTest() {
		$this->Leaders->Session->destroy();
		unset($this->Leaders);		
		ClassRegistry::flush();
	}

	function testIndex() {
		$vars = $this->testAction('/test_leaders/index/Involvement:1', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Leader', $vars['leaders']);
		$expected = array(
			array(
				'Leader' => array(
					'id' => 2,
					'user_id' => 1,
					'model' => 'Involvement',
					'model_id' => 1,
					'created' => '2010-04-09 07:28:57',
					'modified' => '2010-04-09 07:28:57'
				)
			)
		);
		$this->assertEqual($results, $expected);
	}

	function testAdd() {
		$data = array(
			'Leader' => array(
				'user_id' => 2,
				'model' => 'Involvement',
				'model_id' => 1
			)
		);
		$vars = $this->testAction('/test_leaders/add/Involvement:1', array(
			'return' => 'vars',
			'data' => $data
		));
		$this->assertEqual($vars['type'], 'leading');
		$this->assertEqual($vars['itemType'], 'Involvement');
		$this->assertEqual($vars['itemName'], 'CORE 2.0 testing');
		$this->assertEqual($vars['name'], 'ricky rockharbor');

		$results = $this->Leaders->Leader->User->Notification->find('count');
		$this->assertEqual($results, 7);
	}

	function testDelete() {
		$vars = $this->testAction('/test_leaders/delete/Involvement:1/User:1');
		$results = $this->Leaders->Leader->User->Notification->find('count');
		$this->assertEqual($results, 6);
	}

}
?>