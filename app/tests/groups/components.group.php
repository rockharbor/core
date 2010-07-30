<?php

class ComponentsGroupTest extends TestSuite {

	var $label = 'Component tests';

	function ComponentsGroupTest() {
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'components' . DS . 'notifier');
	}
}
