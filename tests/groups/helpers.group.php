<?php

class HelpersGroupTest extends TestSuite {

	var $label = 'Helper tests';

	function HelpersGroupTest() {
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'helpers' . DS . 'app_helper');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'helpers' . DS . 'formatting');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'helpers' . DS . 'permission');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'helpers' . DS . 'select_options');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'helpers' . DS . 'report');
	}
}
