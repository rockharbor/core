<?php

class BehaviorsGroupTest extends TestSuite {

	var $label = 'Behavior tests';

	function BehaviorsGroupTest() {
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'behaviors' . DS . 'confirm');
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'behaviors' . DS . 'geo_coordinate');
	}
}
