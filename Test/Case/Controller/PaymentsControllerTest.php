<?php
/* Payments Test cases generated on: 2010-07-16 08:07:32 : 1279295912 */
App::uses('CoreTestCase', 'Lib');
App::uses('PaymentsController', 'Controller');
App::uses('CreditCard', 'Model');
App::uses('QueueEmailComponent', 'QueueEmail.Controller/Component');
App::uses('AuthorizeDotNetComponent', 'Controller/Component');

Mock::generatePartial('AuthorizeDotNetComponent', 'TestPaymentsControllerAuthorizeDotNetComponent', array('_request'));
Mock::generatePartial('PaymentsController', 'TestPaymentsController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));
Mock::generatePartial('QueueEmailComponent', 'MockPaymentsQueueEmailComponent', array('_smtp', '_mail'));

class PaymentsControllerTestCase extends CoreTestCase {

	public function startTest($method) {
		parent::startTest($method);
		$this->Payments =& new TestPaymentsController();
		$this->Payments->__construct();
		$this->Payments->constructClasses();
		$this->Payments->Notifier->QueueEmail = new MockPaymentsQueueEmailComponent();
		$this->Payments->Notifier->QueueEmail->enabled = true;
		$this->Payments->Notifier->QueueEmail->initialize($this->Payments);
		$this->Payments->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Payments->Notifier->QueueEmail->setReturnValue('_mail', true);
		$CreditCard = new CreditCard();
		$CreditCard->setGateway(new TestPaymentsControllerAuthorizeDotNetComponent());
		ClassRegistry::removeObject('CreditCard');
		ClassRegistry::addObject('CreditCard', $CreditCard);
		ClassRegistry::init('CreditCard');
		// necessary fixtures
		$this->loadFixtures('Payment', 'User', 'Roster', 'PaymentType',
		'PaymentOption', 'Involvement', 'InvolvementType', 'Profile',
		'Address', 'Leader');
		$this->testController = $this->Payments;
	}

	public function endTest() {
		$this->Payments->Session->destroy();
		unset($this->Payments);
		ClassRegistry::flush();
	}

	public function testIndex() {
		$vars = $this->testAction('/payments/index/User:1', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Payment/id', $vars['payments']);
		$expected = array(1);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/payments/index/User:2', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Payment/id', $vars['payments']);
		$expected = array(2, 3);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/payments/index/User:2/Roster:5', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Payment/id', $vars['payments']);
		$expected = array();
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/payments/index/User:2/Roster:4', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Payment/id', $vars['payments']);
		$expected = array(2, 3);
		$this->assertEqual($results, $expected);

		$results = $this->Payments->MultiSelect->getSelected();
		$expected = 'all';
		$this->assertEqual($results, $expected);
	}

	public function testFailedPayment() {
		$this->su(array(
			'User' => array('id' => 1),
			'Group' => array('id' => 1),
			'Profile' => array('primary_email' => 'test@test.com')
		));

		$data = array(
			'Payment' => array(
				'payment_type_id' => 1,
				'amount' => 10
			),
			'CreditCard' => array(
				'address_line_1' => '123 Main St.',
				'address_line_2' => null,
				'city' => 'Anytown',
				'state' => 'CA',
				'zip' => '12345',
				'first_name' => 'Joe',
				'last_name' => 'Schmoe',
				'credit_card_number' => 'invalid',
				'cvv' => '123',
				'expiration_date' => array(
					'month' => '04',
					'year' => '2080',
				)
			)
		);

		// invalid card number
		$vars = $this->testAction('/payments/add/1/Involvement:1', array(
			'return' => 'vars',
			'data' => $data
		));
		$result = $this->Payments->Payment->CreditCard->validationErrors;
		$this->assertTrue(array_key_exists('credit_card_number', $result));
		$result = $this->Payments->Payment->validationErrors;
		$this->assertTrue(empty($result));

		// valid card, invalid gateway response
		$data['CreditCard']['credit_card_number'] = '4242424242424242';
		ClassRegistry::getObject('CreditCard')->getGateway()->setReturnValueAt(0, '_request', '0|||some error|||123456');

		$result = $this->Payments->Payment->CreditCard->validationErrors;
		$this->assertTrue(array_key_exists('credit_card_number', $result));
		$this->assertTrue($result['credit_card_number'], 'some error');
	}

	public function testAdd() {
		ClassRegistry::getObject('CreditCard')->getGateway()->setReturnValue('_request', '1||||||123456');

		$this->su(array(
			'User' => array('id' => 1),
			'Group' => array('id' => 1),
			'Profile' => array('primary_email' => 'test@test.com')
		));
		$this->Payments->Session->write('MultiSelect.test', array(
			'selected' => array(2,1)
		));

		$data = array(
			'Payment' => array(
				'payment_type_id' => 1,
				'amount' => 100
			),
			'CreditCard' => array(
				'address_line_1' => '123 Main St.',
				'address_line_2' => null,
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

		// too much
		$vars = $this->testAction('/payments/add/0/Involvement:1/mstoken:test', array(
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

		// split between 2 people
		$notificationCountBefore = $this->Payments->Payment->User->Notification->find('count');
		$data['Payment']['amount'] = 10;
		$vars = $this->testAction('/payments/add/0/Involvement:1/mstoken:test', array(
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
		$amounts = Set::extract('/Payment/amount', $results);
		$expected = array(5, 5);
		$this->assertEqual($amounts, $expected);
		$numbers = Set::extract('/Payment/number', $results);
		$this->assertEqual($numbers, array(8888, 8888));
		$notificationCountAfter = $this->Payments->Payment->User->Notification->find('count');
		$this->assertEqual($notificationCountAfter-$notificationCountBefore, 1);

		// pay the rest of 1 person who only has 5 left, then the other 20 on the other
		$data['Payment']['amount'] = 25;
		$vars = $this->testAction('/payments/add/0/Involvement:1/mstoken:test', array(
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

		$total = Set::apply('/Payment/amount', $results, 'array_sum');
		$this->assertIdentical($total, 35.00);

		$results = Set::extract('/Payment/amount', $results);
		$expected = array(5, 5, 5, 20);
		$this->assertEqual($results, $expected);

		// add a cash credit
		$data = array(
			'Payment' => array(
				'amount' => -5,
				'payment_type_id' => 2
			)
		);
		$vars = $this->testAction('/payments/add/1/Involvement:1', array(
			'return' => 'vars',
			'data' => $data
		));
		$results = $this->Payments->Payment->find('all', array(
			'conditions' => array(
				'Payment.roster_id' => 1
			)
		));

		$total = Set::apply('/Payment/amount', $results, 'array_sum');
		$this->assertIdentical($total, 5.00);

		// check money sanitization
		$data = array(
			'Payment' => array(
				'amount' => '$5.00',
				'payment_type_id' => 2
			)
		);
		$vars = $this->testAction('/payments/add/1/Involvement:1', array(
			'return' => 'vars',
			'data' => $data
		));

		$result = $this->Payments->Payment->validationErrors;
		$expected = array();
		$this->assertEqual($result, $expected);

		$results = $this->Payments->Payment->find('all', array(
			'conditions' => array(
				'Payment.roster_id' => 1
			)
		));

		$total = Set::apply('/Payment/amount', $results, 'array_sum');
		$this->assertIdentical($total, 10.00);
	}

	public function testEdit() {
		$this->loadFixtures('Group');

		$data = array(
			'Payment' => array(
				'id' => 1,
				'comment' => 'test'
			)
		);
		$vars = $this->testAction('/payments/edit/1', array(
			'return' => 'vars',
			'data' => $data
		));
		$payment = $this->Payments->Payment->read(null, 1);
		$result = $payment['Payment']['comment'];
		$expected = 'test';
		$this->assertEqual($result, $expected);
		$this->assertTrue(empty($this->Payments->Payment->validationErrors));
		$this->assertTrue(!empty($vars['paymentTypes']));
	}

	public function testDelete() {
		$this->testAction('/payments/delete/1');
		$this->assertFalse($this->Payments->Payment->read(null, 1));
	}

}
