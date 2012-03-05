<?php
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Invitation');

class InvitationTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Invitation', 'InvitationsUser');
		$this->Invitation =& ClassRegistry::init('Invitation');
	}

	function endTest() {
		unset($this->Invitation);
		ClassRegistry::flush();
	}

	function testGetInvitations() {
		$results = $this->Invitation->getInvitations(1);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);
		
		$results = $this->Invitation->getInvitations(2);
		$expected = array(2);
		$this->assertEqual($results, $expected);
		
		$results = $this->Invitation->getInvitations(3);
		$expected = array(2);
		$this->assertEqual($results, $expected);
	}

}
?>