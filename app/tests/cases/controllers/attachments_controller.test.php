<?php
/* Payments Test cases generated on: 2010-07-16 08:07:32 : 1279295912 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('Notifier', 'QueueEmail.QueueEmail'));
App::import('Controller', 'UserImages');
App::import('Behavior', 'Media.Transfer');

Mock::generatePartial('QueueEmailComponent', 'MockQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('NotifierComponent', 'MockNotifierComponent', array('_render'));
Mock::generatePartial('UserImagesController', 'MockUserImagesController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));
Mock::generatePartial('TransferBehavior', 'MockTransferBehavior', array('transfer'));

class UserImagesControllerTestCase extends CoreTestCase {

	function startTest() {
		$this->Attachments =& new MockUserImagesController();
		$this->Attachments->__construct();
		$this->Attachments->constructClasses();
		$this->Attachments->Notifier = new MockNotifierComponent();
		$this->Attachments->Notifier->initialize($this->Attachments);
		$this->Attachments->Notifier->setReturnValue('_render', 'Notification body text');
		$this->Attachments->Notifier->QueueEmail = new MockQueueEmailComponent();
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

}
?>