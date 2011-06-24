<?php
/* Invitations Test cases generated on: 2010-07-09 10:07:32 : 1278696092 */
App::import('Lib', 'CoreTestCase');
App::import('Controller', 'Invitations');

Mock::generatePartial('InvitationsController', 'TestInvitationsController', array('isAuthorized', 'render', 'redirect', '_stop', 'header', 'requestAction'));

class InvitationsControllerTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('Invitation', 'InvitationsUser');
		$this->Invitations =& new TestInvitationsController();
		$this->Invitations->__construct();
		$this->Invitations->constructClasses();
		$this->Invitations->setReturnValue('isAuthorized', true);
		$this->testController = $this->Invitations;
	}

	function endTest() {
		$this->Invitations->Session->destroy();
		unset($this->Invitations);		
		ClassRegistry::flush();
	}

	function testIndex() {
		$vars = $this->testAction('/Invitations/index');
		$results = Set::extract('/Invitation/id', $vars['invitations']);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);
		
		$this->Invitations->Session->write('Auth.User.id', 100);
		$vars = $this->testAction('/Invitations/index');
		$results = Set::extract('/Invitation/id', $vars['invitations']);
		$expected = array();
		$this->assertEqual($results, $expected);
	}
	
	function testConfirm() {
		$this->testAction('/invitations/confirm');
		$results = $this->Invitations->Session->read('Message.flash.element');
		$this->assertEqual($results, 'flash'.DS.'failure');
		
		$this->testAction('/invitations/confirm/12/0');
		$results = $this->Invitations->Session->read('Message.flash.element');
		$this->assertEqual($results, 'flash'.DS.'failure');
		
		$this->Invitations->setReturnValueAt(0, 'requestAction', true);
		$this->testAction('/invitations/confirm/1/0');
		$results = $this->Invitations->Session->read('Message.flash.element');
		$this->assertEqual($results, 'flash'.DS.'success');
		
		$beforeCount = $this->Invitations->Invitation->find('count');
		$this->Invitations->setReturnValueAt(1, 'requestAction', false);
		$this->testAction('/invitations/confirm/2/1');
		$results = $this->Invitations->Session->read('Message.flash.element');
		$this->assertEqual($results, 'flash'.DS.'failure');
		$afterCount = $this->Invitations->Invitation->find('count');
		$this->assertEqual($beforeCount-$afterCount, 0);
		
		$userInvitesBefore = count($this->Invitations->Invitation->getInvitations(1));
		$this->Invitations->setReturnValueAt(2, 'requestAction', true);
		$this->testAction('/invitations/confirm/2/1');
		$results = $this->Invitations->Session->read('Message.flash.element');
		$this->assertEqual($results, 'flash'.DS.'success');
		$userInvitesAfter = count($this->Invitations->Invitation->getInvitations(1));
		$this->assertEqual($userInvitesBefore-$userInvitesAfter, 1);
	}
}
?>