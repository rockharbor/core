<?php
/* Payments Test cases generated on: 2010-07-16 08:07:32 : 1279295912 */
App::uses('CoreTestCase', 'Lib');
App::uses('QueueEmailComponent', 'QueueEmail.Controller/Component');
App::uses('ProxyUserImagesController', 'Controller');

Mock::generatePartial('QueueEmailComponent', 'MockAttachmentsQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('ProxyUserImagesController', 'MockProxyUserImagesController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class AttachmentsControllerTestCase extends CoreTestCase {

	public function startTest($method) {
		parent::startTest($method);
		$this->Attachments =& new MockProxyUserImagesController();
		$this->Attachments->__construct();
		$this->Attachments->constructClasses();
		$this->Attachments->Notifier->QueueEmail = new MockAttachmentsQueueEmailComponent();
		$this->Attachments->Notifier->QueueEmail->enabled = true;
		$this->Attachments->Notifier->QueueEmail->initialize($this->Attachments);
		$this->Attachments->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Attachments->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->testController = $this->Attachments;
	}

	public function endTest() {
		$this->Attachments->Session->destroy();
		unset($this->Attachments);
		ClassRegistry::flush();
	}

	public function testGetLimit() {
		$this->loadSettings();

		$controller = new MockProxyUserImagesController();

		$result = $controller->_getLimit();
		$expected = 1;
		$this->assertEqual($result, $expected);

		$result = $controller->_getLimit('User', 'Document');
		$expected = 3;
		$this->assertEqual($result, $expected);

		$result = $controller->_getLimit('SysEmail', 'Document');
		$expected = 2;
		$this->assertEqual($result, $expected);

		$this->unloadSettings();
	}

	public function testBeforeFilter() {
		$vars = $this->testAction('/user_images/index/User:1');
		$this->assertEqual($vars['attachmentModel'], 'Image');
		$this->assertEqual($vars['model'], 'User');
		$this->assertEqual($vars['modelId'], '1');
	}

	public function testDownload() {
		$this->loadFixtures('Attachment');

		$vars = $this->testAction('/user_images/download/4');

		$result = $vars['name'];
		$expected = 'Profile photo';
		$this->assertEqual($result, $expected);

		$result = $vars['download'];
		$expected = true;
		$this->assertEqual($result, $expected);

		$result = $vars['extension'];
		$expected = 'jpg';
		$this->assertEqual($result, $expected);

		$result = $vars['mimeType'];
		$expected = array(
			'jpg' => 'image/jpeg'
		);
		$this->assertEqual($result, $expected);

		$result = $vars['path'];
		$expected = '/'.preg_quote(MEDIA_TRANSFER, '/').'/';
		$this->assertPattern($expected, $result);
	}

	public function testUpload() {
		$this->loadFixtures('Attachment');

		$vars = $this->testAction('/user_images/upload/User:1');

		$result = Set::extract('/Image/id', $vars['attachments']);
		sort($result);
		$expected = array(4);
		$this->assertEqual($result, $expected);

		$result = $vars['limit'];
		$expected = 1;
		$this->assertEqual($result, $expected);
	}

	public function testApprove() {
		$this->testAction('/user_images/approve/4/1');
		$result = $this->Attachments->Session->read('Message.flash.element', 'flash'.DS.'success');

		$this->testAction('/user_images/approve/4/0');
		$this->assertFalse($this->Attachments->Image->read(null, 4));
		$result = $this->Attachments->Session->read('Message.flash.element', 'flash'.DS.'success');
	}

	public function testPromote() {
		$this->loadFixtures('Attachment');
		$this->Attachments->model = 'Involvement';

		$this->Attachments->Session->write('MultiSelect.test', array(
			'selected' => array(1, 2)
		));
		$this->testAction('/involvement_images/promote/0/1/mstoken:test');
		$results = $this->Attachments->Image->find('all', array(
			'conditions' => array(
				'id' => array(6, 7)
			)
		));
		$results = Set::extract('/Image/promoted', $results);
		$expected = array(1, 0);
		$this->assertEqual($results, $expected);

		$this->testAction('/involvement_images/promote/1/0');
		$results = $this->Attachments->Image->read(null, 6);
		$results = Set::extract('/Image/promoted', $results);
		$expected = array(0);
		$this->assertEqual($results, $expected);

		$this->Attachments->model = 'Ministry';

		$this->Attachments->Session->write('MultiSelect.test', array(
			'selected' => array(1, 2)
		));
		$this->testAction('/ministry_images/promote/0/1/mstoken:test');
		$results = $this->Attachments->Image->find('all', array(
			'conditions' => array(
				'id' => array(8, 9)
			)
		));
		$results = Set::extract('/Image/promoted', $results);
		$expected = array(1, 0);
		$this->assertEqual($results, $expected);

		$this->testAction('/ministry_images/promote/1/0');
		$results = $this->Attachments->Image->find('all', array(
			'conditions' => array(
				'id' => array(8, 9)
			)
		));
		$results = Set::extract('/Image/promoted', $results);
		$expected = array(0, 0);
		$this->assertEqual($results, $expected);
	}

}
