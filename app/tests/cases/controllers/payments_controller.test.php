<?php
/* Payments Test cases generated on: 2010-07-16 08:07:32 : 1279295912 */
App::import('Lib', 'CoreTestCase');
App::import('Controller', 'Payments');

Mock::generatePartial('PaymentsController', 'TestPaymentsController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class PaymentsControllerTestCase extends CoreTestCase {

	function startTest() {
		$this->Payments =& new TestPaymentsController();
		$this->Payments->__construct();
		$this->Payments->constructClasses();
		// necessary fixtures
		$this->loadFixtures('Payment', 'User', 'Roster', 'PaymentType', 
		'PaymentOption', 'Involvement', 'InvolvementType', 'Profile',
		'Address');
		$this->testController = $this->Payments;
	}

	function endTest() {
		$this->Payments->Session->destroy();
		unset($this->Payments);		
		ClassRegistry::flush();
	}

	function testIndex() {
		$vars = $this->testAction('/payments/index/User:1', array(
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
					'created' => '2010-05-04 07:33:03',
					'modified' => '2010-05-04 07:33:03',
					'comment' => 'Jeremy Harris\'s card processed by Jeremy Harris.'
				)
			)
		);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/payments/index/User:2', array(
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
					'created' => '2010-05-04 07:33:03',
					'modified' => '2010-05-04 07:33:03',
					'comment' => 'Ricky made a cash payment to pay his balance.'
				)
			)
		);
		$this->assertEqual($results, $expected);
	}

	function testAdd() {
		$this->Payments->Session->write('Auth.User.id', 1);
		$this->Payments->Session->write('User.Group.id', 1);
		$this->Payments->Session->write('User.Profile.primary_email', 'test@test.com');
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

		$vars = $this->testAction('/payments/add/test/Involvement:1', array(
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
		$vars = $this->testAction('/payments/add/test/Involvement:1', array(
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
		$vars = $this->testAction('/payments/add/test/Involvement:1', array(
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
		$this->testAction('/payments/delete/1');
		$this->assertFalse($this->Payments->Payment->read(null, 1));
	}

}
?>