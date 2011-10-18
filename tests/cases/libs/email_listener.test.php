<?php

App::import('Component', array('Notifier'));
App::import('Core', 'Controller');
App::import('Lib', array('CoreTestCase', 'Email'));

Mock::generatePartial('NotifierComponent', 'MockEmailListenerNotifierComponent', array('notify'));

class EmailListenerTestCase extends CoreTestCase {

	function startTest() {
		$this->loadSettings();
		$this->EmailListener = new EmailListener();
	}

	function endTest() {
		unset($this->EmailListener);
		$this->unloadSettings();
	}

	function testError() {
		$this->EmailListener->Notifier = new MockEmailListenerNotifierComponent();
		$this->EmailListener->Notifier->initialize(new Controller());
		$this->EmailListener->Notifier->setReturnValue('notify', true);
		$this->assertNoErrors();
		$this->EmailListener->error(array(
			'message' => 'Some error',
			'file' => 'file.php',
			'line' => 2,
			'level' => E_USER_ERROR
		));
	}

	function testGetNotifier() {
		$result = $this->EmailListener->_getNotifier();
		$this->assertIsA($result, 'NotifierComponent');
	}

	function testGetEmailUsers() {
		$users = $this->EmailListener->_getEmailUsers();
		$this->assertIdentical($users, array('1'));
	}

}

?>
