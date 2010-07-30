<?php

class HelpersGroupTest extends TestSuite {

	var $label = 'Helper tests';

	function HelpersGroupTest() {
		TestManager::addTestFile($this, APP . 'tests' . DS . 'cases' . DS . 'helpers' . DS . 'formatting');
	}
}
