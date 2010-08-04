<?php
/* Leaders Test cases generated on: 2010-07-14 12:07:47 : 1279136267 */
App::import('Lib', 'CoreTestCase');
App::import('Component', 'QueueEmail');
App::import('Controller', 'InvolvementLeaders');

Mock::generate('QueueEmailComponent');
Mock::generatePartial('InvolvementLeadersController', 'TestLeadersController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class LeadersControllerTestCase extends CoreTestCase {
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
		$this->loadFixtures('Leader', 'User', 'Profile', 'Involvement', 'Notification', 'Group');
		$this->Leaders->Component->initialize($this->Leaders);
		$this->Leaders->QueueEmail = new MockQueueEmailComponent();
		$this->Leaders->setReturnValue('isAuthorized', true);
		$this->Leaders->QueueEmail->setReturnValue('send', true);
		$this->testController = $this->Leaders;
	}

	function endTest() {
		unset($this->Leaders);		
		ClassRegistry::flush();
	}

	function testIndex() {
		$vars = $this->testAction('involvement_leaders/index/Involvement:1', array(
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
		$vars = $this->testAction('/involvement_leaders/add/Involvement:1', array(
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
		$vars = $this->testAction('/involvement_leaders/delete/Involvement:1/User:1');
		$results = $this->Leaders->Leader->User->Notification->find('count');
		$this->assertEqual($results, 6);
	}

}
?>