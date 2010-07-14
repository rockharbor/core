<?php
/* Involvements Test cases generated on: 2010-07-12 11:07:51 : 1278959751 */
App::import('Controller', 'Involvements');

class TestInvolvementsController extends InvolvementsController {
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

class InvolvementsControllerTestCase extends CakeTestCase {
	var $fixtures = array('app.notification', 'app.user', 'app.group',
		'app.profile', 'app.classification', 'app.job_category', 'app.school',
		'app.campus', 'plugin.media.attachment', 'app.ministry',
		'app.involvement', 'app.involvement_type', 'app.address',
		'app.zipcode', 'app.region', 'app.date', 'app.payment_option',
		'app.question', 'app.roster', 'app.role', 'app.roster_status',
		'app.answer', 'app.payment', 'app.payment_type', 'app.leader',
		'app.comment', 'app.comment_type', 'app.comments', 'app.notification',
		'app.image', 'plugin.media.document', 'app.household_member',
		'app.household', 'app.publication', 'app.publications_user',
		'app.log', 'app.app_setting', 'app.alert', 'app.alerts_user', 'app.aro',
		'app.aco', 'app.aros_aco', 'app.ministries_rev', 'app.involvements_rev',
		'app.error');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		$this->loadFixtures('Involvement', 'Roster', 'User', 'InvolvementType', 'Group', 'Ministry');
		$this->loadFixtures('InvolvementsRev', 'MinistriesRev');
		$this->loadFixtures('Aco', 'Aro', 'ArosAco', 'Group', 'Error');
		$this->Involvements =& new TestInvolvementsController();
		$this->Involvements->constructClasses();
		$this->Involvements->Component->initialize($this->Involvements);
		$this->Involvements->Session->write('Auth.User', array('id' => 1));
		$this->Involvements->Session->write('User', array('Group' => array('id' => 1)));
	}

	function endTest() {
		$this->Involvements->Session->destroy();
		unset($this->Involvements);		
		ClassRegistry::flush();
	}

	function testInviteRoster() {
		$vars = $this->testAction('/test_involvements/invite_roster/1/Involvement:2');
		$invites = $this->Involvements->Involvement->Roster->User->Notification->find('all', array(
			'conditions' => array(
				'Notification.type' => 'invitation'
			)
		));
		$this->assertEqual(count($invites), 2);
	}

	function testInvite() {
		$vars = $this->testAction('/test_involvements/invite/1/Involvement:2');
		$invites = $this->Involvements->Involvement->Roster->User->Notification->find('all', array(
			'conditions' => array(
				'Notification.type' => 'invitation'
			)
		));
		$this->assertEqual(count($invites), 1);
	}

	function testAdd() {
		$data = array(
			'Involvement' => array(
				'ministry_id' => 4,
				'involvement_type_id' => 1,
				'name' => 'A test involvement',
				'description' => 'this is a test',
				'roster_limit' => null,
				'roster_visible' => 1,
				'group_id' => NULL,
				'signup' => 1,
				'take_payment' => 1,
				'offer_childcare' => 0,
				'active' => 1,
				'force_payment' => 0
			)
		);
		$this->testAction('/test_involvements/add', array(
			'data' => $data
		));
		$this->Involvements->Involvement->id = 5;
		$this->assertEqual($this->Involvements->Involvement->field('name'), 'A test involvement');
		$this->assertEqual($this->Involvements->Involvement->field('group_id'), 0);
	}

	function testEdit() {
		$data = $this->Involvements->Involvement->read(null, 1);
		$data['Involvement']['name'] = 'New name';

		$this->Involvements->Session->write('User', array('Group' => array('id' => 5)));
		$vars = $this->testAction('/test_involvements/edit/Involvement:1', array(
			'return' => 'vars',
			'data' => $data
		));
		$this->Involvements->Involvement->id = 1;
		$result = $this->Involvements->Involvement->field('name');
		$this->assertEqual($result, 'CORE 2.0 testing');
		$result = $this->Involvements->Involvement->RevisionModel->field('name');
		$this->assertEqual($result, 'New name');

		$this->Involvements->Involvement->RevisionModel->delete(1);
		$this->Involvements->Session->write('User', array('Group' => array('id' => 1)));
		$vars = $this->testAction('/test_involvements/edit/Involvement:1', array(
			'return' => 'vars',
			'data' => $data
		));
		$this->Involvements->Involvement->id = 1;
		$this->assertEqual($this->Involvements->Involvement->field('name'), 'New name');
	}

	function testToggleActivity() {
		$this->testAction('/test_involvements/toggle_activity/1/Involvement:3');
		$this->Involvements->Involvement->id = 3;
		$this->assertEqual($this->Involvements->Session->read('TestCase.flash.element'), 'flash_failure');

		$this->Involvements->Session->delete('TestCase');
		$data = array(
			'PaymentOption' => array(
				'involvement_id' => 3,
				'name' => 'pay for me!',
				'total' => 89,
				'deposit' => 54,
				'childcare' => NULL,
				'account_code' => '123456',
				'tax_deductible' => 1
			)
		);
		$this->Involvements->Involvement->PaymentOption->save($data);
		$this->testAction('/test_involvements/toggle_activity/1/Involvement:3');
		$this->Involvements->Involvement->id = 3;
		$this->assertEqual($this->Involvements->Involvement->field('active'), 1);
		$this->assertEqual($this->Involvements->Session->read('TestCase.flash.element'), 'flash_success');
	}

	function testHistory() {
		$data = $this->Involvements->Involvement->read(null, 1);
		$data['Involvement']['name'] = 'New name';
		$this->Involvements->Session->write('User', array('Group' => array('id' => 5)));
		$vars = $this->testAction('/test_involvements/edit/Involvement:1', array(
			'return' => 'vars',
			'data' => $data
		));

		$vars = $this->testAction('/test_involvements/history/Involvement:1', array(
			'return' => 'vars'
		));

		$result = $vars['revision']['Revision']['id'];
		$this->assertEqual($result, 1);

		$result = $vars['revision']['Revision']['name'];
		$this->assertEqual($result, 'New name');
	}

	function testRevise() {
		$data = $this->Involvements->Involvement->read(null, 1);
		$data['Involvement']['name'] = 'New name';
		$this->Involvements->Session->write('User', array('Group' => array('id' => 5)));

		$vars = $this->testAction('/test_involvements/edit/Involvement:1', array(
			'return' => 'vars',
			'data' => $data
		));
		$this->Involvements->Involvement->id = 1;
		$this->testAction('/test_involvements/revise/0/Involvement:1');
		$result = $this->Involvements->Involvement->RevisionModel->find('all');
		$this->assertFalse($result);
		$result = $this->Involvements->Involvement->field('name');
		$this->assertEqual($result, 'CORE 2.0 testing');

		$vars = $this->testAction('/test_involvements/edit/Involvement:1', array(
			'return' => 'vars',
			'data' => $data
		));
		$this->testAction('/test_involvements/revise/1/Involvement:1');
		$result = $this->Involvements->Involvement->RevisionModel->find('all');
		$this->assertFalse($result);
		$result = $this->Involvements->Involvement->field('name');
		$this->assertEqual($result, 'New name');
	}

	function testDelete() {
		$this->testAction('/test_involvements/delete/1');
		$this->assertFalse($this->Involvements->Involvement->read(null, 1));
	}

}
?>