<?php
App::import('Lib', 'CoreTestCase');
App::import('Controller', 'App');
App::import('Component', 'AuthorizeDotNet');

Mock::generatePartial('AuthorizeDotNetComponent', 'MockAuthorizeDotNetComponent', array('_request'));

if (!isset($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = 'http://www.example.com';
}

class TestAuthorizeController extends AppController {}

class AuthorizeDotNetTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('User', 'Profile');
		$this->loadSettings();
		$this->AuthorizeDotNet = new MockAuthorizeDotNetComponent();
		$this->Controller = new TestAuthorizeController();
	}

	function endTest() {
		unset($this->AuthorizeDotNet);
		unset($this->Controller);
		$this->unloadSettings();
		ClassRegistry::flush();
	}

	function testSetInvoiceNumber() {
		$this->AuthorizeDotNet->setInvoiceNumber('invoice. 12345&#$');
		$result = $this->AuthorizeDotNet->_data['x_invoice_num'];
		$expected = 'invoice12345';
		$this->assertEqual($result, $expected);

		$this->AuthorizeDotNet->setInvoiceNumber('invoice. 123456549879874654321321&#$');
		$result = $this->AuthorizeDotNet->_data['x_invoice_num'];
		$expected = 'invoice1234565498798';
		$this->assertEqual($result, $expected);
	}

	function testSetDescription() {
		$this->AuthorizeDotNet->setDescription(' string &*@!#$& with <b>symbols</b>');
		$result = $this->AuthorizeDotNet->_data['x_description'];
		$expected = 'string  with symbols';
		$this->assertEqual($result, $expected);
	}

	function testSetAmount() {
		$this->AuthorizeDotNet->setAmount(2000);
		$result = $this->AuthorizeDotNet->_data['x_Amount'];
		$expected = '2000.00';
		$this->assertEqual($result, $expected);

		$this->AuthorizeDotNet->setAmount(2000.52);
		$result = $this->AuthorizeDotNet->_data['x_Amount'];
		$expected = '2000.52';
		$this->assertEqual($result, $expected);
	}

	function testSetCustomer() {
		$this->AuthorizeDotNet->setCustomer();
		$this->assertFalse(isset($this->AuthorizeDotNet->_data['x_First_Name']));

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
		$result = $this->AuthorizeDotNet->_data['x_First_Name'];
		$expected = 'jeremy';
		$this->assertEqual($result, $expected);

		$result = $this->AuthorizeDotNet->_data['x_Exp_Date'];
		$expected = '092012';
		$this->assertEqual($result, $expected);

		$result = $this->AuthorizeDotNet->_data['x_Address'];
		$expected = '123 Main Apt B';
		$this->assertEqual($result, $expected);
	}

	function testSetInvoice() {
		$this->AuthorizeDotNet->setInvoice('test');
		$result = $this->AuthorizeDotNet->_data['x_Invoice'];
		$expected = 'test';
		$this->assertEqual($result, $expected);
	}

	function testFormatFields() {
		$data = array(
			'Key' => 'Value',
			'Something' => 'Nothing',
			'this' => 'is that'
		);
		$result = $this->AuthorizeDotNet->_formatFields($data);
		$expected = 'Key=Value&Something=Nothing&this=is+that';
		$this->assertEqual($result, $expected);
	}

	function testInit() {
		$User = ClassRegistry::init('User');
		$User->contain(array('Profile'));
		$AppSetting = ClassRegistry::init('AppSetting');

		$this->AuthorizeDotNet->_init();
		$result = $this->AuthorizeDotNet->_data['x_Merchant_Email'];
		$expected = 'jharris@rockharbor.org';
		$this->assertEqual($result, $expected);
		$result = $this->AuthorizeDotNet->_data['x_Test_Request'];
		$expected = 'TRUE';
		$this->assertEqual($result, $expected);

		Configure::write('debug', 0);
		$this->AuthorizeDotNet->_init();
		$result = $this->AuthorizeDotNet->_data['x_Merchant_Email'];
		$expected = 'cc@example.com';
		$this->assertEqual($result, $expected);
		$result = $this->AuthorizeDotNet->_data['x_Test_Request'];
		$expected = 'FALSE';
		$this->assertEqual($result, $expected);
		Configure::write('debug', 2);
	}

	function testRequest() {
		$this->AuthorizeDotNet->setReturnValue('_request', '1||||||123456');
		$this->AuthorizeDotNet->_data = array(
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
		);
		$results = $this->AuthorizeDotNet->request();
		$this->assertTrue($results);

		$this->AuthorizeDotNet->_data['x_Login'] = Configure::read('AuthorizeDotNet.username');
		$this->AuthorizeDotNet->_data['x_Password'] = Configure::read('AuthorizeDotNet.password');
	}

}
