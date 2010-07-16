<?php
/* PaymentOptions Test cases generated on: 2010-07-16 11:07:27 : 1279303767 */
App::import('Controller', 'PaymentOptions');

class TestPaymentOptionsController extends PaymentOptionsController {
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

class PaymentOptionsControllerTestCase extends CakeTestCase {
	var $fixtures = array('app.notification', 'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.roster', 'app.role', 'app.roster_status', 'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member', 'app.household', 'app.publication', 'app.publications_user', 'app.log', 'app.app_setting', 'app.alert', 'app.alerts_user', 'app.aro', 'app.aco', 'app.aros_aco', 'app.ministries_rev', 'app.involvements_rev', 'app.error', 'app.log');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		$this->PaymentOptions =& new TestPaymentOptionsController();
		$this->PaymentOptions->constructClasses();
		// necessary fixtures
		$this->loadFixtures('Aco', 'Aro', 'ArosAco', 'Group', 'Error');
		$this->loadFixtures('PaymentOption');
		$this->PaymentOptions->Component->initialize($this->PaymentOptions);
		$this->PaymentOptions->Session->write('Auth.User', array('id' => 1));
		$this->PaymentOptions->Session->write('User', array('Group' => array('id' => 1)));
	}

	function endTest() {
		$this->PaymentOptions->Session->destroy();
		unset($this->PaymentOptions);		
		ClassRegistry::flush();
	}

	function testIndex() {
		$vars = $this->testAction('/test_payment_options/index/Involvement:1', array(
			'return' => 'vars'
		));
		$results = Set::extract('/PaymentOption', $vars['paymentOptions']);
		$expected = array(
			array(
				'PaymentOption' => array(
					'id' => 1,
					'involvement_id' => 1,
					'name' => 'Single Person',
					'total' => 25,
					'deposit' => NULL,
					'childcare' => NULL,
					'account_code' => '123',
					'tax_deductible' => 0,
					'created' => '2010-04-08 13:35:34',
					'modified' => '2010-04-08 13:35:34'
				)
			),
			array(
				'PaymentOption' => array(
					'id' => 2,
					'involvement_id' => 1,
					'name' => 'Single Person with Childcare',
					'total' => 25,
					'deposit' => NULL,
					'childcare' => 10,
					'account_code' => '123',
					'tax_deductible' => 0,
					'created' => '2010-04-08 13:41:16',
					'modified' => '2010-04-09 10:20:25'
				)
			)
		);
		$this->assertEqual($results, $expected);
	}

	function testAdd() {
		$data = array(
			'PaymentOption' => array(
				'involvement_id' => 3,
				'name' => 'Team CORE signups that cost more',
				'total' => 15,
				'deposit' => '',
				'childcare' => '',
				'account_code' => '456',
				'tax_deductible' => 1
			)
		);

		$this->testAction('/test_payment_options/add', array(
			'data' => $data
		));

		$paymentOption = $this->PaymentOptions->PaymentOption->read(null, 4);
		$result = $paymentOption['PaymentOption']['name'];
		$this->assertEqual($result, 'Team CORE signups that cost more');
	}

	function testEdit() {
		$data = $this->PaymentOptions->PaymentOption->read(null, 1);
		$data['PaymentOption']['name'] = 'New name';

		$this->testAction('/test_payment_options/edit/1', array(
			'data' => $data
		));

		$this->PaymentOptions->PaymentOption->id = 1;
		$result = $this->PaymentOptions->PaymentOption->field('name');
		$this->assertEqual($result, 'New name');
	}

	function testDelete() {
		$this->testAction('/test_payment_options/delete/1');
		$this->assertFalse($this->PaymentOptions->PaymentOption->read(null, 1));
	}

}
?>