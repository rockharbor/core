<?php

class ComponentsGroupTest extends TestSuite {

	public $label = 'Component tests';

	public function ComponentsGroupTest() {
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'components' . DS . 'authorize_dot_net');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'components' . DS . 'notifier');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'components' . DS . 'filter_pagination');
	}
}
