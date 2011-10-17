<?php

class ControllersGroupTest extends TestSuite {

	var $label = 'Controller tests';

	function ControllersGroupTest() {
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'addresses_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'alerts_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'app_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'app_settings_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'attachments_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'campuses_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'comments_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'dates_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'households_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'invitations_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'involvement_leaders_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'involvements_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'leaders_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'merge_requests_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'ministries_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'ministry_leaders_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'notifications_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'pages_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'payment_options_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'payments_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'publications_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'notifications_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'reports_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'rosters_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'searches_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'sys_emails_controller');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'controllers' . DS . 'users_controller');
	}
}
