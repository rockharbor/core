<?php
/* AppSettings Test cases generated on: 2010-07-09 14:07:19 : 1278709879 */
App::import('Lib', 'CoreTestCase');
App::import('Controller', 'AppSettings');

Mock::generatePartial('AppSettingsController', 'TestAppSettingsController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class AppSettingsControllerTestCase extends CoreTestCase {
	var $fixtures = array('app.ministries_rev', 'app.involvements_rev','app.app_setting', 'app.log', 'app.notification', 'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.roster', 'app.role', 'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member', 'app.household', 'app.publication', 'app.publications_user', 'app.log', 'app.app_setting', 'app.alert', 'app.alerts_user', 'app.error');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		$this->loadFixtures('AppSetting', 'Publication');
		$this->AppSettings =& new TestAppSettingsController();
		$this->AppSettings->constructClasses();
		$this->AppSettings->setReturnValue('isAuthorized', true);
		$this->testController = $this->AppSettings;
	}

	function endTest() {
		unset($this->AppSettings);		
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