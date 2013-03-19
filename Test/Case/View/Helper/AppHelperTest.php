<?php
App::uses('CoreTestCase', 'Lib');
App::uses('AppHelper', 'View/Helper');

/**
 * Proxy class to allow access to protected methods
 */
class ProxyAppHelper extends AppHelper {
	public function selectedArray($data, $key = 'id') {
		$this->__selectedArray($data, $key);
	}
}

class AppHelperTestCase extends CoreTestCase {

	public function startTest($method) {
		parent::startTest($method);
		$this->App = new ProxyAppHelper();
	}

	public function endTest() {
		unset($this->App);
		ClassRegistry::flush();
	}

	public function testSelectedArray() {
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