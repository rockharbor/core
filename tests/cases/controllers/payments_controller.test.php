<?php
/* Payments Test cases generated on: 2010-07-16 08:07:32 : 1279295912 */
App::import('Lib', 'CoreTestCase');
App::import('Controller', 'Payments');
App::import('Model', 'CreditCard');
App::import('Component', array('QueueEmail.QueueEmail'));

Mock::generatePartial('PaymentsController', 'TestPaymentsController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));
Mock::generatePartial('CreditCard', 'MockPaymentsCreditCard', array('save', 'saveAll'));
Mock::generatePartial('QueueEmailComponent', 'MockPaymentsQueueEmailComponent', array('_smtp', '_mail'));

class PaymentsControllerTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->Payments =& new TestPaymentsController();
		$this->Payments->__construct();
		$this->Payments->constructClasses();
		$this->Payments->Notifier->QueueEmail = new MockPaymentsQueueEmailComponent();
		$this->Payments->Notifier->QueueEmail->enabled = true;
		$this->Payments->Notifier->QueueEmail->initialize($this->Payments);
		$this->Payments->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Payments->Notifier->QueueEmail->setReturnValue('_mail', true);
		$CreditCard =& new MockPaymentsCreditCard();
		$CreditCard->something = 'nothing';
		$CreditCard->setReturnValue('save', true);
		$CreditCard->setReturnValue('saveAll', true);
		ClassRegistry::removeObject('CreditCard');
		ClassRegistry::addObject('CreditCard', $CreditCard);
		ClassRegistry::init('CreditCard');
		// necessary fixtures
		$this->loadFixtures('Payment', 'User', 'Roster', 'PaymentType', 
		'PaymentOption', 'Involvement', 'InvolvementType', 'Profile',
		'Address', 'Leader');
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
		$results = Set::extract('/Payment/id', $vars['payments']);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/payments/index/User:2', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Payment/id', $vars['payments']);
		$expected = array(2, 3);
		$this->assertEqual($results, $expected);
	}

	function testAdd() {
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

		// split between 2 people
		$notificationCountBefore = $this->Payments->Payment->User->Notification->find('count');
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
		$amounts = Set::extract('/Payment/amount', $results);
		$expected = array(5, 5);
		$this->assertEqual($amounts, $expected);
		$numbers = Set::extract('/Payment/number', $results);
		$this->assertEqual($numbers, array(8888, 8888));
		$notificationCountAfter = $this->Payments->Payment->User->Notification->find('count');
		$this->assertEqual($notificationCountAfter-$notificationCountBefore, 1);

		// pay the rest of 1 person who only has 5 left, then the other 20 on the other
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