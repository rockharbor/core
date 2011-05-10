<?php
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Image');

class UserTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('Attachment');
		$this->Image = ClassRegistry::init('Image');
	}

	function endTest() {
		unset($this->Image);
		ClassRegistry::flush();
	}

	function testCustomImageSizes() {
		$default = Configure::read('Core.mediafilters.default');

		$custom = Configure::read('Core.mediafilters.user');
		$data = array(
			'model' => 'User'
		);
		$this->Image->create();
		$this->Image->save($data);
		$this->assertEqual(Configure::read('Media.filter.image'), $custom);

		$data = array(
			'model' => 'Unknown'
		);
		$this->Image->create();
		$this->Image->save($data);
		$this->assertEqual(Configure::read('Media.filter.image'), $default);

		$custom = Configure::read('Core.mediafilters.involvement');
		$data = array(
			'model' => 'Involvement'
		);
		$this->Image->create();
		$this->Image->save($data);
		$this->assertEqual(Configure::read('Media.filter.image'), $custom);
	}

}