<?php
/* AppSettings Test cases generated on: 2010-07-09 14:07:19 : 1278709879 */
App::import('Lib', 'CoreTestCase');
App::import('Controller', 'AppSettings');

Mock::generatePartial('AppSettingsController', 'TestAppSettingsController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class AppSettingsControllerTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('AppSetting', 'Publication', 'User');
		$this->AppSettings =& new TestAppSettingsController();
		$this->AppSettings->constructClasses();
		$this->AppSettings->setReturnValue('isAuthorized', true);
		$this->testController = $this->AppSettings;
	}

	function endTest() {
		$this->AppSettings->AppSetting->clearCache();
		unset($this->AppSettings);
		ClassRegistry::flush();
	}

	function testEdit() {
		$data = $this->AppSettings->AppSetting->read(null, 1);
		$data['AppSetting']['value'] = 'Other Church';
		$this->testAction('/app_settings/edit/1', array(
			'data' => $data
		));
		$this->AppSettings->AppSetting->id = 1;
		$this->assertEqual($this->AppSettings->AppSetting->field('value'), 'Other Church');

		$vars = $this->testAction('/app_settings/edit/9');
		$expected = array(
			1 => 'ebulletin',
			2 => 'Family Ministry Update'
		);
		$this->assertEqual($vars['valueOptions'], $expected);
	}

	function testSanitizeHtml() {
		$data = $this->AppSettings->AppSetting->read(null, 1);
		$data['AppSetting']['value'] = '<span>Other Church</span>';
		$this->testAction('/app_settings/edit/1', array(
			'data' => $data
		));
		$this->AppSettings->AppSetting->id = 1;
		$this->assertEqual($this->AppSettings->AppSetting->field('value'), '&lt;span&gt;Other Church&lt;/span&gt;');
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