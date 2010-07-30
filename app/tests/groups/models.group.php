<?php

class ModelsGroupTest extends TestSuite {

	var $label = 'Model tests';

	function ModelsGroupTest() {
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'alert');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'app_model');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'campus');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'date');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'group');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'household');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'involvement');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'leader');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'ministry');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'roster');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'user');
	}
}
