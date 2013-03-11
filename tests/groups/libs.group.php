<?php

class LibsGroupTest extends TestSuite {

	public $label = 'Lib tests';

	public function LibsGroupTest() {
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'libs' . DS . 'core');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'libs' . DS . 'core_test_case');
	}
}
