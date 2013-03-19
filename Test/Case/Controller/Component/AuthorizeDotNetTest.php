<?php
App::uses('CoreTestCase', 'Lib');
App::uses('AppController', 'Controller');
App::uses('ProxyAuthorizeDotNetComponent', 'Controller/Component');

if (!isset($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = 'http://www.example.com';
}

class TestAuthorizeController extends AppController {}

Mock::generatePartial('ProxyAuthorizeDotNetComponent', 'MockAuthorizeDotNetComponent', array('_request'));

class AuthorizeDotNetTestCase extends CoreTestCase {

	public function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('User', 'Profile');
		$this->loadSettings();
		$this->AuthorizeDotNet = new MockAuthorizeDotNetComponent();
		$this->Controller = new TestAuthorizeController();
	}

	public function endTest() {
		unset($this->AuthorizeDotNet);
		unset($this->Controller);
		$this->unloadSettings();
		ClassRegistry::flush();
	}

	public function testSetInvoiceNumber() {
		$this->AuthorizeDotNet->setInvoiceNumber('invoice. 12345&#$');
		$result = $this->AuthorizeDotNet->getData('x_invoice_num');
		$expected = 'invoice12345';
		$this->assertEqual($result, $expected);

		$this->AuthorizeDotNet->setInvoiceNumber('invoice. 123456549879874654321321&#$');
		$result = $this->AuthorizeDotNet->getData('x_invoice_num');
		$expected = 'invoice1234565498798';
		$this->assertEqual($result, $expected);
	}

	public function testSetDescription() {
		$this->AuthorizeDotNet->setDescription(' string &*@!#$& with <b>symbols</b>');
		$result = $this->AuthorizeDotNet->getData('x_description');
		$expected = 'string  with symbols';
		$this->assertEqual($result, $expected);
	}

	public function testSetAmount() {
		$this->AuthorizeDotNet->setAmount(2000);
		$result = $this->AuthorizeDotNet->getData('x_Amount');
		$expected = '2000.00';
		$this->assertEqual($result, $expected);

		$this->AuthorizeDotNet->setAmount(2000.52);
		$result = $this->AuthorizeDotNet->getData('x_Amount');
		$expected = '2000.52';
		$this->assertEqual($result, $expected);
	}

	public function testSetCustomer() {
		$this->AuthorizeDotNet->setCustomer();
		$results = $this->AuthorizeDotNet->getData();
		$this->assertFalse(isset($results['x_First_Name']));

		$this->AuthorizeDotNet->setCustomer(array(
			'first_name' => 'jeremy',
			'last_name' => 'harris',
			'credit_card_number' => '1234123412341234',
			'cvv' => '123',
			'expiration_date' => array(
				'month' => '09',
				'year' => '2012'
			),
			'address_line_1' => '123 Main',
			'address_line_2' => 'Apt B',
			'city' => 'Nowhere',
			'state' => 'CA',
			'zip' => '12345',
			'email' => 'test@example.com',
		));
		$result = $this->AuthorizeDotNet->getData('x_First_Name');
		$expected = 'jeremy';
		$this->assertEqual($result, $expected);

		$result = $this->AuthorizeDotNet->getData('x_Exp_Date');
		$expected = '092012';
		$this->assertEqual($result, $expected);

		$result = $this->AuthorizeDotNet->getData('x_Address');
		$expected = '123 Main Apt B';
		$this->assertEqual($result, $expected);
	}

	public function testSetInvoice() {
		$this->AuthorizeDotNet->setInvoice('test');
		$result = $this->AuthorizeDotNet->getData('x_Invoice');
		$expected = 'test';
		$this->assertEqual($result, $expected);
	}

	public function testFormatFields() {
		$data = array(
			'Key' => 'Value',
			'Something' => 'Nothing',
			'this' => 'is that'
		);
		$result = $this->AuthorizeDotNet->_formatFields($data);
		$expected = 'Key=Value&Something=Nothing&this=is+that';
		$this->assertEqual($result, $expected);
	}

	public function testInit() {
		$User = ClassRegistry::init('User');
		$User->contain(array('Profile'));
		$AppSetting = ClassRegistry::init('AppSetting');

		$this->AuthorizeDotNet->_init();
		$result = $this->AuthorizeDotNet->getData('x_Merchant_Email');
		$expected = 'jharris@rockharbor.org';
		$this->assertEqual($result, $expected);
		$result = $this->AuthorizeDotNet->getData('x_Test_Request');
		$expected = 'TRUE';
		$this->assertEqual($result, $expected);

		Configure::write('debug', 0);
		$this->AuthorizeDotNet->_init();
		$result = $this->AuthorizeDotNet->getData('x_Merchant_Email');
		$expected = 'cc@example.com';
		$this->assertEqual($result, $expected);
		$result = $this->AuthorizeDotNet->getData('x_Test_Request');
		$expected = 'FALSE';
		$this->assertEqual($result, $expected);
		Configure::write('debug', 2);
	}

	public function testRequest() {
		$this->AuthorizeDotNet->setReturnValue('_request', '1||||||123456');
		$this->AuthorizeDotNet->setData(array(
			'x_First_Name' => 'Jeremy',
			'x_Last_Name' => 'Harris',
			'x_Card_Num' => '4007000000027',
			'x_card_code' => '123',
			'x_Exp_Date' => '0412',
			'x_Address' => '123 Fake St.',
			'x_City' => 'Springfield',
			'x_State' => 'No One Knows',
			'x_Zip' => '12345',
			'x_Email' => 'test@test.com',
			'x_Amount' => 20
		));
		$results = $this->AuthorizeDotNet->request();
		$this->assertTrue($results);
	}

}
