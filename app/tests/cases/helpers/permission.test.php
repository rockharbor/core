<?php
App::import('Helper', array('Permission', 'Js', 'Html'));
App::import('Controller', 'App');

Mock::generate('HtmlHelper');
Mock::generate('JsHelper');
Mock::generatePartial('AppController', 'MockAppController', array('isAuthorized'));

class PermissionHelperTestCase extends CakeTestCase {

	function startTest() {
		$this->Permission =& new PermissionHelper();
		$this->Permission->Html = new MockHtmlHelper();
		$this->Permission->Js = new MockJsHelper();
	}

	function endTest() {
		unset($this->Permission);
		ClassRegistry::flush();
	}

	function testLink() {
		$View = new View(new Controller(), true);
		$View->set('activeUser', array(
			'Group' => array(
				'id' => 1
			)
		));
		$this->Permission->AppController = new MockAppController();
		$this->Permission->AppController->setReturnValue('isAuthorized', true);

		$this->Permission->AppController->setReturnValueAt(0, 'isAuthorized', false);
		$result = $this->Permission->link('Disallowed', array('controller' => 'dontletme', 'action' => 'gethere'));
		$this->assertNull($result);

		$this->Permission->Html->expectOnce('link');
		$result = $this->Permission->link('Allowed', array('controller' => 'users', 'action' => 'delete'));
		
		$this->Permission->Js->expectOnce('link');
		$result = $this->Permission->link('Allowed', array('controller' => 'users', 'action' => 'delete'), array('complete' => 'CORE.update()'));
	}

	function testNoView() {
		$this->assertNoErrors();
		$this->Permission->beforeRender();
	}

	function testGetNonExistentPermission() {
		$this->assertFalse($this->Permission->can('seeSomething'));
	}

	function testGetPermission() {
		$View = new View(new Controller(), true);
		$View->set('_canPerformAction', true);
		$View->set('_canPerformAnotherAction', false);
		$this->Permission->beforeRender();

		$this->assertTrue($this->Permission->can('performAction'));
		$this->assertFalse($this->Permission->can('performAnotherAction'));
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