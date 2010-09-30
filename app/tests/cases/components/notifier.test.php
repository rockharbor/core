<?php
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Notification');
App::import('Component', array('Notifier', 'QueueEmail.QueueEmail'));

Mock::generatePartial('QueueEmailComponent', 'MockQueueEmailComponent', array('send'));

class TestNotifierController extends Controller {}

class NotifierTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('Notification');
		$this->Notifier = new NotifierComponent();
		$this->Controller = new TestNotifierController();
		$this->Notifier->initialize($this->Controller, array());
		$this->Notification = ClassRegistry::init('Notification');
		$this->Notification->QueueEmail = new MockQueueEmailComponent();
		$this->Notification->QueueEmail->initialize($this->Controller);
		$this->Notification->QueueEmail->setReturnValue('send', true);
	}

	function endTest() {
		unset($this->Notifier);
		unset($this->Notification);
		unset($this->Controller);
		ClassRegistry::flush();
	}

	function testNotify() {
		$this->Controller->set('name', 'Jeremy');
		$this->Controller->set('type', 'leading');
		$this->Controller->set('itemType', 'Team');
		$this->Controller->set('itemName', 'Core Developers');

		$this->assertTrue($this->Notifier->notify(1, 'leaders_add'));
		$this->assertFalse($this->Notifier->notify(1, 'nonexistantnotification'));
	}

	function testSend() {

	}

	function testSave() {
		$dateReg = 'preg:/([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?/';

		$data = array(
			$this->Notifier->foreignKey => 1,
			$this->Notifier->contentField => 'A notification'
		);

		unset($this->Notifier->notification);
		$this->Notifier->_save($data);
		$this->assertError('Notifier::_save() : Notification model does not exist.');
		$this->Notifier->notification = $this->Notification;

		$this->Notifier->_save($data);
		$this->Notification->recursive = -1;
		$result = $this->Notification->read(array('user_id', 'body', 'read', 'type'));
		$expected = array(
			$this->Notifier->notification->name => array(
				'user_id' => 1,
				'body' => 'A notification',
				'read' => false,
				'type' => null
			)
		);
		$this->assertEqual($result, $expected);
		
		$this->Notifier->saveData = array(
			'type' => 'invitation'
		);
		$this->Notifier->_save($data);
		$this->Notification->recursive = -1;
		$result = $this->Notification->read(array('user_id', 'body', 'read', 'type'));
		$expected = array(
			$this->Notifier->notification->name => array(
				'user_id' => 1,
				'body' => 'A notification',
				'read' => false,
				'type' => 'invitation'
			)
		);
		$this->assertEqual($result, $expected);
	}

	function testRender() {
		$this->Controller->set('name', 'Jeremy');
		$this->Controller->set('type', 'leading');
		$this->Controller->set('itemType', 'Team');
		$this->Controller->set('itemName', 'Core Developers');

		$expected = 'Jeremy is now leading the team Core Developers.';
		$result = $this->Notifier->_render('leaders_add');
		$this->assertEqual($result, $expected);

		$this->assertFalse($this->Notifier->_render('nonexistantnotification'));
	}

}



?>
