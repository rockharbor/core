<?php
App::import('Lib', 'CoreTestCase');
App::import('Helper', array('Permission', 'Js', 'Html'));
App::import('Controller', 'App');

Mock::generate('HtmlHelper');
Mock::generate('JsHelper');
Mock::generatePartial('AppController', 'MockAppController', array('isAuthorized'));

class PermissionHelperTestCase extends CoreTestCase {

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

	function testLinkNoController() {
		$View = new View(new Controller(), true);
		$View->set('activeUser', array(
			'Group' => array(
				'id' => 1
			)
		));
		$this->Permission->params['controller'] = 'awesome_users';
		$this->Permission->AppController = new MockAppController();
		$this->Permission->AppController->setReturnValue('isAuthorized', true);
		
		$this->Permission->Html = new HtmlHelper();
		
		$result = $this->Permission->link('Allowed', array('action' => 'delete'));
		$this->assertPattern('/awesome_users\/delete/', $result);
	}

	function testNoView() {
		$this->assertNoErrors();
		$this->Permission->beforeRender();
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
		$this->assertTrue($this->Permission->check(array('controller' => 'a', 'action' => 'path')));
	}
	
	function testCanSeePrivate() {
		$this->loadFixtures('Group');
		
		$view = new View(new Controller());
		$view->viewVars['activeUser'] = array(
			'Group' => array(
				'id' => 1
			)
		);
		
		// allowed
		$this->assertTrue($this->Permission->canSeePrivate());
		
		$view->viewVars['activeUser'] = array(
			'Group' => array(
				'id' => 8
			)
		);
		
		// cached
		$this->assertTrue($this->Permission->canSeePrivate());
		$this->Permission->_canSeePrivate = null;
		// not allowed
		$this->assertFalse($this->Permission->canSeePrivate());
	}

}