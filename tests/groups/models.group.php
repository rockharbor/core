<?php

class ModelsGroupTest extends TestSuite {

	public $label = 'Model tests';

	public function ModelsGroupTest() {
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'address');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'alert');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'app_model');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'campus');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'comment');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'date');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'group');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'household');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'image');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'invitation');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'involvement');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'ministry');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'profile');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'role');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'roster');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'school');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'sys_email');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'models' . DS . 'user');
	}
}
