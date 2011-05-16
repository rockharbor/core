<?php
App::import('Lib', 'CoreTestCase');
App::import('Controller', 'App');

class AppControllerTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('User', 'Group', 'Notification', 'Alert', 'Household', 'HouseholdMember');
		$this->loadFixtures('Leader', 'Campus', 'Ministry', 'Involvement');
		$this->App =& new AppController();		
		$this->App->__construct();
		$this->App->constructClasses();
		$this->App->Component->initialize($this->App);
		$this->App->activeUser = array(
			'User' => array('id' => 1),
			'Group' => array('id' => 1)
		);
	}

	function endTest() {
		$this->App->Session->destroy();
		unset($this->App);
		ClassRegistry::flush();
	}

	function testSetConditionalGroups() {
		$this->App->passedArgs = array('User' => 1);
		$results = $this->App->_setConditionalGroups($this->App->passedArgs, $this->App->activeUser);
		$results = Set::extract('/Group/name', $results);
		$expected = array('Household Contact', 'Owner');
		$this->assertEqual($results, $expected);

		$this->App->activeUser['User']['id'] = 2;
		$this->App->passedArgs = array('User' => 3);
		$results = $this->App->_setConditionalGroups($this->App->passedArgs, $this->App->activeUser);
		$results = Set::extract('/Group/name', $results);
		$expected = array('Household Contact');
		$this->assertEqual($results, $expected);

		$this->App->activeUser['User']['id'] = 3;
		$this->App->passedArgs = array('Campus' => 1);
		$results = $this->App->_setConditionalGroups($this->App->passedArgs, $this->App->activeUser);
		$this->assertFalse($results);

		$this->App->activeUser['User']['id'] = 1;
		$this->App->passedArgs = array('Campus' => 1);
		$results = $this->App->_setConditionalGroups($this->App->passedArgs, $this->App->activeUser);
		$results = Set::extract('/Group/name', $results);
		$expected = array('Campus Manager');
		$this->assertEqual($results, $expected);

		$this->App->activeUser['User']['id'] = 1;
		$this->App->passedArgs = array('Ministry' => 4);
		$results = $this->App->_setConditionalGroups($this->App->passedArgs, $this->App->activeUser);
		$results = Set::extract('/Group/name', $results);
		$expected = array('Ministry Manager');
		$this->assertEqual($results, $expected);

		$this->App->activeUser['User']['id'] = 1;
		$this->App->passedArgs = array('Involvement' => 1);
		$results = $this->App->_setConditionalGroups($this->App->passedArgs, $this->App->activeUser);
		$results = Set::extract('/Group/name', $results);
		$expected = array('Involvement Leader');
		$this->assertEqual($results, $expected);
		
		$this->App->activeUser['User']['id'] = 2;
		$this->App->passedArgs = array('Involvement' => 1);
		$results = $this->App->_setConditionalGroups($this->App->passedArgs, $this->App->activeUser);
		$results = $results = Set::extract('/Group/name', $results);
		$expected = array('Ministry Manager');
		$this->assertEqual($results, $expected);
		
		$this->App->activeUser['User']['id'] = 1;
		$this->App->passedArgs = array('Involvement' => 1, 'Ministry' => 4);
		$results = $this->App->_setConditionalGroups($this->App->passedArgs, $this->App->activeUser);
		$results = $results = Set::extract('/Group/name', $results);
		$expected = array('Involvement Leader', 'Ministry Manager');
		$this->assertEqual($results, $expected);
	}

	function testIsAuthorized() {
		$this->App->Acl = new MockAclComponent();

		$this->App->activeUser = array(
			'User' => array('id' => 1),
			'Group' => array('id' => 8)
		);

		$this->App->Acl->setReturnValueAt(0, 'check', false);
		$result = $this->App->isAuthorized('involvements/delete');
		$this->assertFalse($result);

		$this->App->Acl->setReturnValueAt(1, 'check', true);
		$result = $this->App->isAuthorized('involvements/delete');
		$this->assertTrue($result);

		$this->App->passedArgs = array('User' => 1);
		$this->App->Acl->setReturnValueAt(2, 'check', false);
		$this->App->Acl->setReturnValueAt(3, 'check', true);
		$result = $this->App->isAuthorized('involvements/delete');
		$this->assertTrue(isset($this->App->activeUser['ConditionalGroup']['id']));
		$this->assertTrue($result);

		$this->App->passedArgs = array('User' => 10);
		$this->App->Acl->setReturnValueAt(4, 'check', true);
		$result = $this->App->isAuthorized('involvements/delete');
		$this->assertTrue($result);

		$this->App->Acl->setReturnValueAt(5, 'check', false);
		$this->App->Acl->setReturnValueAt(6, 'check', true);
		$result = $this->App->isAuthorized('involvements/delete', array('User' => 1));
		$this->assertTrue(isset($this->App->activeUser['ConditionalGroup']['id']));
		$this->assertTrue($result);

		$this->App->passedArgs = array('User' => 2);
		$this->App->Acl->setReturnValueAt(7, 'check', true);
		$this->App->Acl->setReturnValueAt(8, 'check', true);
		$user = array('User' => array('id' => 2), 'Group' => array('id' => 8));
		$result = $this->App->isAuthorized('involvements/delete', array(), $user);
		$this->assertTrue(isset($user['ConditionalGroup']['id']));
		$this->assertEqual($user['User']['id'], 2);
		$this->assertTrue($result);
	}

	function test_editSelf() {
		$this->App->action = 'edit';
		$this->App->_editSelf('edit');
		$results = $this->App->_setConditionalGroups($this->App->passedArgs, $this->App->activeUser);
		$expected = array(
			'id' => 12,
			'name' => 'Owner',
			'conditional' => 1,
			'created' => '2010-07-07 11:15:22',
			'modified' => '2010-07-07 11:15:22',
			'parent_id' => 8,
			'lft' => 15,
			'rght' => 18
		);
		$this->assertEqual($results, $expected);
	}
}

?>