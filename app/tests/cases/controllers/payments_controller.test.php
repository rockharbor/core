<?php
/* Payments Test cases generated on: 2010-07-16 08:07:32 : 1279295912 */
App::import('Controller', 'Payments');

class TestPaymentsController extends PaymentsController {
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
}

class PaymentsControllerTestCase extends CakeTestCase {
	var $fixtures = array('app.notification', 'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.roster', 'app.role', 'app.roster_status', 'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member', 'app.household', 'app.publication', 'app.publications_user', 'app.log', 'app.app_setting', 'app.alert', 'app.alerts_user', 'app.aro', 'app.aco', 'app.aros_aco', 'app.ministries_rev', 'app.involvements_rev', 'app.error', 'app.log');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		$this->Payments =& new TestPaymentsController();
		$this->Payments->constructClasses();
		// necessary fixtures
		$this->loadFixtures('Aco', 'Aro', 'ArosAco', 'Group', 'Error');
		$this->loadFixtures('Payment', 'User', 'Roster', 'PaymentType', 
		'PaymentOption', 'Involvement', 'InvolvementType', 'Profile',
		'Address');
		$this->Payments->Component->initialize($this->Payments);
		$this->Payments->Session->write('Auth.User', array('id' => 1));
		$this->Payments->Session->write('User', array('Group' => array('id' => 1)));
	}

	function endTest() {
		$this->Payments->Session->destroy();
		unset($this->Payments);		
		ClassRegistry::flush();
	}

	function testIndex() {
		$vars = $this->testAction('/test_payments/index/User:1', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Payment', $vars['payments']);
		$expected = array(
			array(
				'Payment' => array(
					'id' => 1,
					'user_id' => 1,
					'roster_id' => 4,
					'amount' => 25,
					'payment_type_id' => 1,
					'number' => '0027',
					'transaction_id' => '1234',
					'payment_placed_by' => 1,
					'refunded' => 0,
					'payment_option_id' => 1,
					'created' => '2010-05-04 07:33:03',
					'modified' => '2010-05-04 07:33:03',
					'comment' => 'Jeremy Harris\'s card processed by Jeremy Harris.'
				)
			),
			array(
				'Payment' => array(
					'id' => 2,
					'user_id' => 2,
					'roster_id' => 4,
					'amount' => 2.50,
					'payment_type_id' => 1,
					'number' => '0027',
					'transaction_id' => '1234',
					'payment_placed_by' => 1,
					'refunded' => 0,
					'payment_option_id' => 3,
					'created' => '2010-05-04 07:33:03',
					'modified' => '2010-05-04 07:33:03',
					'comment' => 'Jeremy Harris\'s card processed by Jeremy Harris.'
				)
			)
		);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/test_payments/index/User:2', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Payment', $vars['payments']);
		$expected = array(
			array(
				'Payment' => array(
					'id' => 2,
					'user_id' => 2,
					'roster_id' => 4,
					'amount' => 2.50,
					'payment_type_id' => 1,
					'number' => '0027',
					'transaction_id' => '1234',
					'payment_placed_by' => 1,
					'refunded' => 0,
					'payment_option_id' => 3,
					'created' => '2010-05-04 07:33:03',
					'modified' => '2010-05-04 07:33:03',
					'comment' => 'Jeremy Harris\'s card processed by Jeremy Harris.'
				)
			),
			array(
				'Payment' => array(
					'id' => 3,
					'user_id' => 2,
					'roster_id' => 4,
					'amount' => 2.50,
					'payment_type_id' => 2,
					'number' => NULL,
					'transaction_id' => NULL,
					'payment_placed_by' => 2,
					'refunded' => 0,
					'payment_option_id' => 3,
					'created' => '2010-05-04 07:33:03',
					'modified' => '2010-05-04 07:33:03',
					'comment' => 'Ricky made a cash payment to pay his balance.'
				)
			)
		);
		$this->assertEqual($results, $expected);
	}

	function testAdd() {
		$this->Payments->Session->write('MultiSelect.test', array(
			'selected' => array(2,3)
		));

		$data = array(
			'Payment' => array(
				'payment_type_id' => 1,
				'amount' => 100
			),
			'CreditCard' => array(
				'address_line_1' => '123 Main St.',
				'city' => 'Anytown',
				'state' => 'CA',
				'zip' => '12345',
				'first_name' => 'Joe',
				'last_name' => 'Schmoe',
				'credit_card_number' => '4012888818888',
				'cvv' => '123',
				'expiration_date' => array(
					'month' => '04',
					'year' => '2080',
				)
			)
		);

		$vars = $this->testAction('/test_payments/add/test/Involvement:1', array(
			'return' => 'vars',
			'data' => $data
		));
		$results = $this->Payments->Payment->find('all', array(
			'conditions' => array(
				'Roster.involvement_id' => 1
			),
			'contain' => array(
				'Roster'
			)
		));
		$this->assertEqual($results, array());

		$data['Payment']['amount'] = 10;
		$vars = $this->testAction('/test_payments/add/test/Involvement:1', array(
			'return' => 'vars',
			'data' => $data
		));
		$results = $this->Payments->Payment->find('all', array(
			'conditions' => array(
				'Roster.involvement_id' => 1
			),
			'contain' => array(
				'Roster'
			)
		));
		$results = Set::extract('/Payment/amount', $results);
		$expected = array(5, 5);
		$this->assertEqual($results, $expected);

		$data['Payment']['amount'] = 25;
		$vars = $this->testAction('/test_payments/add/test/Involvement:1', array(
			'return' => 'vars',
			'data' => $data
		));
		$results = $this->Payments->Payment->find('all', array(
			'conditions' => array(
				'Roster.involvement_id' => 1
			),
			'contain' => array(
				'Roster'
			)
		));

		$total = Set::filter('/Payment/amount', $results, 'array_sum');
		$this->assertEqual($total, 35);

		$results = Set::extract('/Payment/amount', $results);
		$expected = array(5, 5, 5, 20);
		$this->assertEqual($results, $expected);
	}

	function testDelete() {
		$this->testAction('/test_payments/delete/1');
		$this->assertFalse($this->Payments->Payment->read(null, 1));
	}

}
?>