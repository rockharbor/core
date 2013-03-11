<?php
/* PaymentOptions Test cases generated on: 2010-07-16 11:07:27 : 1279303767 */
App::import('Lib', 'CoreTestCase');
App::import('Controller', 'PaymentOptions');

Mock::generatePartial('PaymentOptionsController', 'TestPaymentOptionsController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class PaymentOptionsControllerTestCase extends CoreTestCase {

	public function startTest($method) {
		parent::startTest($method);
		$this->PaymentOptions =& new TestPaymentOptionsController();
		$this->PaymentOptions->__construct();
		$this->PaymentOptions->constructClasses();
		// necessary fixtures
		$this->loadFixtures('PaymentOption');
		$this->testController = $this->PaymentOptions;
	}

	public function endTest() {
		$this->PaymentOptions->Session->destroy();
		unset($this->PaymentOptions);
		ClassRegistry::flush();
	}

	public function testIndex() {
		$vars = $this->testAction('/payment_options/index/Involvement:1', array(
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

	public function testAdd() {
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

		$this->testAction('/payment_options/add/Involvement:3', array(
			'data' => $data
		));

		$paymentOption = $this->PaymentOptions->PaymentOption->read();
		$result = $paymentOption['PaymentOption']['name'];
		$this->assertEqual($result, 'Team CORE signups that cost more');
	}

	public function testEdit() {
		$data = $this->PaymentOptions->PaymentOption->read(null, 1);
		$data['PaymentOption']['name'] = 'New name';

		$this->testAction('/payment_options/edit/1', array(
			'data' => $data
		));

		$this->PaymentOptions->PaymentOption->id = 1;
		$result = $this->PaymentOptions->PaymentOption->field('name');
		$this->assertEqual($result, 'New name');
	}

	public function testDelete() {
		$this->testAction('/payment_options/delete/1');
		$this->assertFalse($this->PaymentOptions->PaymentOption->read(null, 1));
	}

}
