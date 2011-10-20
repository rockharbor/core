<?php
App::import('Lib', 'CoreTestCase');
App::import('Controller', 'App');
App::import('Component', 'AuthorizeDotNet');

if (!isset($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = 'http://www.example.com';
}

class TestAuthorizeController extends AppController {}

class AuthorizeDotNetTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('User', 'Profile');
		$this->loadSettings();
		$this->AuthorizeDotNet = new AuthorizeDotNetComponent();
		$this->Controller = new TestAuthorizeController();
	}

	function endTest() {
		unset($this->AuthorizeDotNet);
		unset($this->Controller);
		$this->unloadSettings();
		ClassRegistry::flush();
	}

	function testInit() {
		$User = ClassRegistry::init('User');
		$User->contain(array('Profile'));
		$AppSetting = ClassRegistry::init('AppSetting');

		$id = Core::read('development.debug_email');

		$debugUser = $User->findById($id);
		$email = $debugUser['Profile']['primary_email'];

		$this->AuthorizeDotNet->_init();
		$result = $this->AuthorizeDotNet->_data['x_Merchant_Email'];
		$this->assertEqual($result, $email);
	}

	function testRequest() {
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
	}

}
?>