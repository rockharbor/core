<?php
App::import('Lib', 'CoreTestCase');
App::import('Controller', 'App');
App::import('Component', 'AuthorizeDotNet');

Mock::generatePartial('AuthorizeDotNetComponent', 'MockAuthorizeDotNetComponent', array('request'));

class TestAuthorizeController extends AppController {}

class AuthorizeDotNetTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('User', 'AppSetting', 'Profile');
		Core::loadSettings(true);
		$this->AuthorizeDotNet = new MockAuthorizeDotNetComponent();
		$this->Controller = new TestAuthorizeController();
		$this->AuthorizeDotNet->setReturnValue('request', true);
	}

	function endTest() {
		unset($this->AuthorizeDotNet);
		unset($this->Controller);
		Cache::delete('core_app_settings');
		ClassRegistry::flush();
	}

	function testInit() {
		$User = ClassRegistry::init('User');
		$User->contain(array('Profile'));
		$AppSetting = ClassRegistry::init('AppSetting');

		$setting = $AppSetting->findByName('debug_email');
		$id = $setting['AppSetting']['value'];

		$debugUser = $User->findById($id);
		$email = $debugUser['Profile']['primary_email'];

		$this->AuthorizeDotNet->_init();
		$result = $this->AuthorizeDotNet->_data['x_Merchant_Email'];
		$this->assertEqual($result, $email);
	}

}



?>
