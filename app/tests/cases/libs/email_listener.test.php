<?php

App::import('Lib', array('CoreTestCase', 'Email'));

class EmailListenerTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('AppSetting');
		Core::loadSettings(true);
		$this->EmailListener = new EmailListener();
	}

	function endTest() {
		unset($this->Whistle);
	}

	function testGetEmailer() {
		$result = $this->EmailListener->_getEmailer();
		$this->assertIsA($result, 'QueueEmailComponent');
	}

	function testGetEmailUsers() {
		$users = $this->EmailListener->_getEmailUsers();
		$this->assertIdentical($users, array('2'));
	}

}

?>
