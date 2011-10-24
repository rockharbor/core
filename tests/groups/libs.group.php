<?php

class LibsGroupTest extends TestSuite {

	var $label = 'Lib tests';

	function LibsGroupTest() {
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'libs' . DS . 'core');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'libs' . DS . 'core_test_case');
	}
}
