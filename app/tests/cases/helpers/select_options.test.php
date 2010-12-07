<?php
App::import('Helper', array('SelectOptions'));

class SelectOptionsHelperTestCase extends CakeTestCase {

	function startTest() {
		$this->SelectOptions = new SelectOptionsHelper();
	}

	function endTest() {
		unset($this->SelectOptions);
		ClassRegistry::flush();
	}

	function testMagicCall() {
		$this->assertFalse(method_exists($this->SelectOptions, 'gender'));
		$this->assertEqual($this->SelectOptions->gender('m'), 'Male');

		$this->assertFalse(method_exists($this->SelectOptions, 'maritalStatus'));
		$this->assertEqual($this->SelectOptions->maritalStatus('d'), 'Divorced');

		$this->assertFalse(method_exists($this->SelectOptions, 'grade'));
		$this->assertEqual($this->SelectOptions->grade(-1), 'Pre-kinder');

		$this->expectError();
		$this->assertFalse($this->SelectOptions->nonExistentMap);
	}

}
?>