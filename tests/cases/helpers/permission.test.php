<?php
App::import('Lib', 'CoreTestCase');
App::import('Helper', array('ProxyPermission', 'Js', 'Html'));
App::import('Controller', array('SysEmails'));

Mock::generate('HtmlHelper');
Mock::generate('JsHelper');
Mock::generatePartial('SysEmailsController', 'MockPermissionHelperTestSysEmailsController', array('isAuthorized'));

class PermissionHelperTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->Permission =& new ProxyPermissionHelper();
		$this->Permission->Html = new MockHtmlHelper();
		$this->Permission->Js = new MockJsHelper();
	}

	function endTest() {
		unset($this->Permission);
		ClassRegistry::flush();
	}

	function testLink() {
		$controller = new MockPermissionHelperTestSysEmailsController();
		$controller->__construct();
		$controller->constructClasses();

		$View = new View($controller, true);
		$View->set('activeUser', array(
			'Group' => array(
				'id' => 1
			)
		));
		$this->Permission->controllers['mock_permission_helper_test_sys_emails'] = $controller;
		$controller->setReturnValue('isAuthorized', true);

		$controller->setReturnValueAt(0, 'isAuthorized', false);
		$result = $this->Permission->link('Disallowed', array('controller' => 'mock_permission_helper_test_sys_emails', 'action' => 'gethere'));
		$this->assertNull($result);

		$this->Permission->Html->expectOnce('link');
		$result = $this->Permission->link('Allowed', array('controller' => 'mock_permission_helper_test_sys_emails', 'action' => 'delete'));

		$this->Permission->Js->expectOnce('link');
		$result = $this->Permission->link('Allowed', array('controller' => 'mock_permission_helper_test_sys_emails', 'action' => 'delete'), array('complete' => 'CORE.update()'));
	}

	function testNoView() {
		$this->assertNoErrors();
		$this->Permission->beforeRender();
	}

	function testCheck() {
		$controller = new MockPermissionHelperTestSysEmailsController();
		$controller->__construct();

		$View = new View($controller, true);
		$View->set('activeUser', array(
			'Group' => array(
				'id' => 1
			)
		));

		// first attempt at isAuthorized will return `null` as it is mocked
		$this->assertFalse($this->Permission->check(array('controller' => 'mock_permission_helper_test_sys_emails', 'action' => 'path')));
		$this->assertTrue(isset($this->Permission->controllers['mock_permission_helper_test_sys_emails']));
		$this->assertIsA($this->Permission->controllers['mock_permission_helper_test_sys_emails'], 'Controller');

		$this->Permission->controllers['mock_permission_helper_test_sys_emails']->setReturnValueAt(1, 'isAuthorized', true);
		$this->assertTrue($this->Permission->check(array('controller' => 'mock_permission_helper_test_sys_emails', 'action' => 'path')));

		$this->Permission->controllers['mock_permission_helper_test_sys_emails']->setReturnValueAt(2, 'isAuthorized', false);
		$this->assertFalse($this->Permission->check(array('controller' => 'mock_permission_helper_test_sys_emails', 'action' => 'path')));

		// still returns true because `AuthComponent` says so in `beforeFilter`
		$this->Permission->controllers['mock_permission_helper_test_sys_emails']->setReturnValueAt(3, 'isAuthorized', false);
		$this->assertTrue($this->Permission->check(array('controller' => 'mock_permission_helper_test_sys_emails', 'action' => 'bug_compose')));
	}

	function testCanSeePrivate() {
		$this->loadFixtures('Group');

		$controller = new MockPermissionHelperTestSysEmailsController();
		$controller->__construct();

		$view = new View($controller);
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

		// cached, so what would usually fail succeeds
		$this->assertTrue($this->Permission->canSeePrivate());
		// force checking again
		$this->Permission->_canSeePrivate(null);
		// not allowed
		$this->assertFalse($this->Permission->canSeePrivate());
	}

}