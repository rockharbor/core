<?php
/* AppSettings Test cases generated on: 2010-07-09 14:07:19 : 1278709879 */
App::import('Lib', 'CoreTestCase');
App::import('Controller', 'AppSettings');

Mock::generatePartial('AppSettingsController', 'TestAppSettingsController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class AppSettingsControllerTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('Publication');
		$this->loadSettings();
		$this->AppSettings =& new TestAppSettingsController();
		$this->AppSettings->constructClasses();
		$this->AppSettings->setReturnValue('isAuthorized', true);
		$this->testController = $this->AppSettings;
	}

	function endTest() {
		unset($this->AppSettings);
		$this->unloadSettings();
		ClassRegistry::flush();
	}

	function testEdit() {
		$data = array(
			'id' => 1,
			'value' => 'Other Church'
		);
		$this->testAction('/app_settings/edit/1', array(
			'data' => $data
		));
		$setting = $this->AppSettings->AppSetting->read(null, 1);
		$this->assertEqual($setting['AppSetting']['value'], 'Other Church');

		$vars = $this->testAction('/app_settings/edit/2');
		$expected = array(
			1 => 'ebulletin',
			2 => 'Family Ministry Update'
		);
		$this->assertEqual($vars['valueOptions'], $expected);
	}

}
?>