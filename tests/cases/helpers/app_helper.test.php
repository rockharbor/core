<?php
App::import('Lib', 'CoreTestCase');
App::import('Helper', array('App'));

/**
 * Proxy class to allow access to protected methods
 */
class ProxyAppHelper extends AppHelper {
	function selectedArray($data, $key = 'id') {
		$this->__selectedArray($data, $key);
	}
}

class AppHelperTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->App = new ProxyAppHelper();
	}

	function endTest() {
		unset($this->App);
		ClassRegistry::flush();
	}

	function testSelectedArray() {
		$data = array(
			'Some' => array(
				'Embedded' => array(
					'model',
					'data',
					'that',
					'is',
					'not',
					'habtm'
				)
			)
		);
		$this->assertEqual($this->App->selectedArray($data), array());
	}

}