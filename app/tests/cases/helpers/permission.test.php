<?php
App::import('Helper', 'Permission');
App::import('Controller', 'App');
Mock::generatePartial('AppController', 'MockAppController', array('isAuthorized'));

class PermissionHelperTestCase extends CakeTestCase {

	function startTest() {
		$this->Permission =& new PermissionHelper();
	}

	function endTest() {
		unset($this->Permission);
		ClassRegistry::flush();
	}

	function testGetNonExistentPermission() {
		$this->assertFalse($this->Permission->canSeeSomething);
	}

	function testGetPermission() {
		$View = new View(new Controller(), true);
		$View->set('_canPerformAction', true);
		$View->set('_canPerformAnotherAction', false);
		$this->Permission->beforeRender();

		$this->assertTrue($this->Permission->canPerformAction);
		$this->assertFalse($this->Permission->canPerformAnotherAction);
	}

	function testCheck() {
		$View = new View(new Controller(), true);
		$View->set('activeUser', array(
			'Group' => array(
				'id' => 1
			)
		));
		$this->Permission->AppController = new MockAppController();
		$this->Permission->AppController->setReturnValue('isAuthorized', true);
		$this->assertTrue($this->Permission->check('/a/path'));
	}

}