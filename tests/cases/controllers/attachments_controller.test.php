<?php
/* Payments Test cases generated on: 2010-07-16 08:07:32 : 1279295912 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail.QueueEmail'));
App::import('Controller', 'UserImages');

Mock::generatePartial('QueueEmailComponent', 'MockAttachmentsQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('UserImagesController', 'MockUserImagesController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class AttachmentsControllerTestCase extends CoreTestCase {

	function startTest() {
		$this->Attachments =& new MockUserImagesController();
		$this->Attachments->__construct();
		$this->Attachments->constructClasses();
		$this->Attachments->Notifier->QueueEmail = new MockAttachmentsQueueEmailComponent();
		$this->Attachments->Notifier->QueueEmail->enabled = true;
		$this->Attachments->Notifier->QueueEmail->initialize($this->Attachments);
		$this->Attachments->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Attachments->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->testController = $this->Attachments;
	}

	function endTest() {
		$this->Attachments->Session->destroy();
		unset($this->Attachments);
		ClassRegistry::flush();
	}

	function testBeforeFilter() {
		$vars = $this->testAction('/user_images/index/User:1');
		$this->assertEqual($vars['attachmentModel'], 'Image');
		$this->assertEqual($vars['model'], 'User');
		$this->assertEqual($vars['modelId'], '1');
	}

	function testApprove() {
		$this->testAction('/user_images/approve/4/1');
		$result = $this->Attachments->Session->read('Message.flash.element', 'flash'.DS.'success');

		$this->testAction('/user_images/approve/4/0');
		$this->assertFalse($this->Attachments->Image->read(null, 4));
		$result = $this->Attachments->Session->read('Message.flash.element', 'flash'.DS.'success');
	}
	
	function testPromote() {
		$this->loadFixtures('Attachment');
		$this->Attachments->model = 'Involvement';
		
		$this->Attachments->Session->write('MultiSelect.test', array(
			'selected' => array(1, 2)
		));
		$this->testAction('/involvement_images/promote/test/1');
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
		$this->testAction('/ministry_images/promote/test/1');
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
?>