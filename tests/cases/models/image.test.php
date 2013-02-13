<?php
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Image');

class ImageTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Attachment');
		$this->Image = ClassRegistry::init('Image');
		$this->Image->Behaviors->detach('Media.Coupler'); // requires 'file' key, which we don't want to test
	}

	function endTest() {
		unset($this->Image);
		ClassRegistry::flush();
	}

	function testTransferTo() {
		$this->Image->model = 'User';
		$uuidReg = "[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}";

		$file = array(
			'file' => 'image.jpg',
			'mimeType' => null
		);
		$result = $this->Image->transferTo(array(), $file);
		$pattern = '/img\\'.DS.'user\\'.DS.$uuidReg.'\.jpg/';
		$this->assertPattern($pattern, $result);

		$this->Image->model = 'Involvement';
		$uuidReg = "[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}";

		$file = array(
			'file' => 'image.mpg',
			'mimeType' => 'video/mpeg'
		);
		$result = $this->Image->transferTo(array(), $file);
		$pattern = '/vid\\'.DS.'involvement\\'.DS.$uuidReg.'\.mpeg/';
		$this->assertPattern($pattern, $result);
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