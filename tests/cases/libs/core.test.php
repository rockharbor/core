<?php

App::import('Lib', array('CoreTestCase'));
App::import('Model', 'AppSetting');

class CoreConfigureTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('AppSetting', 'Attachment');
		$this->AppSetting =& ClassRegistry::init('AppSetting');
		$this->loadSettings();
	}

	function endTest() {
		$this->unloadSettings();
		unset($this->AppSetting);
	}

	function testReadImageSetting() {
		$result = Core::read('users.default_image');
		$expected = 'Default profile photo';
		$this->assertTrue($result['alternative'], $expected);

		$this->assertNull(Core::read('users.default_icon'));
	}

	function testRead() {
		$result = Core::read('version');
		$this->assertTrue(is_string($result));

		$result = Core::read('development.debug_email');
		$this->assertEqual($result, 1);

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

		$result = Core::read('a.deep.array');
		$expected = array(
			'test' => array(
				'something' => 'cool'
			)
		);
		$this->assertEqual($result, $expected);

		$this->assertIdentical(Core::read('UndefinedSetting'), null);

		$this->assertIdentical(Core::read('a.deep.UndefinedSetting'), null);
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
