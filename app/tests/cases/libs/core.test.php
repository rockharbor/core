<?php

App::import('Lib', array('CoreTestCase'));
App::import('Model', 'AppSetting');
if (!class_exists('Cache')) {
	require LIBS . 'cache.php';
}

class CoreConfigureTestCase extends CoreTestCase {

	function startTest() {
		$this->_cacheDisable = Configure::read('Cache.disable');
		Configure::write('Cache.disable', false);
		$this->loadFixtures('AppSetting');
		$this->AppSetting =& ClassRegistry::init('AppSetting');
		Core::loadSettings(true);
	}

	function endTest() {
		Cache::delete('core_app_settings');
		Configure::write('Cache.disable', $this->_cacheDisable);
	}

	function testCache() {
		$this->assertTrue(Cache::read('core_app_settings'));
	}

	function testRead() {
		$result = Core::read('version');
		$this->assertTrue(is_string($result));

		$result = Core::read('debug_email');
		$this->assertEqual($result, 2);

		Core::_write('a.deep.array.test', array(
			'something' => 'cool'
		));
		$result = Core::read('a');
		$expected = array(
			'deep' => array(
				'array' => array(
					'test' => array(
						'something' => 'cool'
					)
				)
			)
		);
		$this->assertEqual($result, $expected);
	}

	function testWrite() {
		$result = Core::_write('test', 'success');
		$expected = 'success';
		$this->assertEqual($result, $expected);

		$result = Core::_write('another.test', 13);
		$expected = array(
			'test' => 13
		);
		$this->assertEqual($result, $expected);

		$result = Core::_write('another.deeper.array.test', 'horray!');
		$expected = array(
			'deeper' => array(
				'array' => array(
					'test' => 'horray!'
				)
			),
			'test' => 13
		);
		$this->assertEqual($result, $expected);

		$result = Core::_write('another', false);
		$expected = false;
		$this->assertIdentical($result, $expected);		
	}
}

?>
