<?php

class BehaviorsGroupTest extends TestSuite {

	public $label = 'Behavior tests';

	public function BehaviorsGroupTest() {
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'behaviors' . DS . 'confirm');
	}
}
