<?php
/* AppSettings Test cases generated on: 2010-07-09 14:07:19 : 1278709879 */
App::import('Lib', 'CoreTestCase');
App::import('Controller', 'AppSettings');

Mock::generatePartial('AppSettingsController', 'TestAppSettingsController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class AppSettingsControllerTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		Router::parseExtensions('json');
		
		$this->loadFixtures('AppSetting', 'User');
		$this->AppSettings =& new TestAppSettingsController();
		$this->AppSettings->__construct();
		$this->AppSettings->constructClasses();
		$this->AppSettings->setReturnValue('isAuthorized', true);
		$this->testController = $this->AppSettings;
	}

	function endTest() {
		$this->AppSettings->AppSetting->clearCache();
		unset($this->AppSettings);
		ClassRegistry::flush();
	}

	function testSearch() {
		$vars = $this->testAction('/app_settings/search/User.json', array(
			'data' => array(
				'AppSetting' => array(
					'value' => 'rick'
				)
			),
			'return' => 'vars'
		));
		$expected = array(
			2 => 'rickyrockharbor',
			3 => 'rickyrockharborjr',
		);
		$this->assertEqual($vars['results'], $expected);
		$this->assertEqual($vars['model'], 'User');
	}

	function testIndex() {
		$vars = $this->testAction('/app_settings/index');
		$this->assertIsA($vars, 'array');
	}

	function testEdit() {
		$data = $this->AppSettings->AppSetting->read(null, 1);
		$data['AppSetting']['value'] = 'Other Church';
		$this->testAction('/app_settings/edit/1', array(
			'data' => $data
		));
		$this->AppSettings->AppSetting->id = 1;
		$this->assertEqual($this->AppSettings->AppSetting->field('value'), 'Other Church');
	}

	function testHtml() {
		$data = $this->AppSettings->AppSetting->read(null, 1);
		$data['AppSetting']['value'] = '<span>Other Church</span>';
		$this->testAction('/app_settings/edit/1', array(
			'data' => $data
		));
		$this->AppSettings->AppSetting->id = 1;
		$this->assertEqual($this->AppSettings->AppSetting->field('value'), '<span>Other Church</span>');
	}

	function testSanitizeString() {
		$data = $this->AppSettings->AppSetting->read(null, 3);
		$data['AppSetting']['value'] = '<span>http://urlwithhtml.com</span>';
		$this->testAction('/app_settings/edit/3', array(
			'data' => $data
		));
		$this->AppSettings->AppSetting->id = 3;
		$this->assertEqual($this->AppSettings->AppSetting->field('value'), 'http://urlwithhtml.com');
	}

	function testSanitizeList() {
		$data = $this->AppSettings->AppSetting->read(null, 21);
		$data['AppSetting']['value'] = '<p>Money</p>,For-nothing,<b>Chicks for free</b>';
		$this->testAction('/app_settings/edit/21', array(
			'data' => $data
		));
		$this->AppSettings->AppSetting->id = 21;
		$this->assertEqual($this->AppSettings->AppSetting->field('value'), 'Money,For-nothing,Chicks for free');
	}

	function testSanitizeInteger() {
		$data = $this->AppSettings->AppSetting->read(null, 14);
		$data['AppSetting']['value'] = 'This1sn\'tAn!Integer<br/>';
		$this->testAction('/app_settings/edit/14', array(
			'data' => $data
		));
		$this->AppSettings->AppSetting->id = 14;
		$this->assertEqual($this->AppSettings->AppSetting->field('value'), 1);
	}
}
?>