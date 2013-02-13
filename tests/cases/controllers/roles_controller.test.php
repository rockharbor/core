<?php
/* Rosters Test cases generated on: 2010-08-05 12:08:42 : 1281037602 */
App::import('Lib', 'CoreTestCase');
App::import('Controller', 'Roles');

Mock::generatePartial('RolesController', 'MockRolesController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class RolesControllerTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Role');
		$this->Roles =& new MockRolesController();
		$this->Roles->__construct();
		$this->Roles->constructClasses();
		$this->Roles->setReturnValue('isAuthorized', true);
		$this->testController = $this->Roles;
	}

	function endTest() {
		$this->Roles->Session->destroy();
		unset($this->Roles);
		ClassRegistry::flush();
	}

	function testIndex() {
		$vars = $this->testAction('/roles/index/Ministry:4');
		$results = Set::extract('/Role/id', $vars['roles']);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);
	}

	function testAdd() {
		$this->loadFixtures('Ministry');

		$countBefore = $this->Roles->Role->find('count');
		$this->testAction('/roles/add/Ministry:3', array(
			'data' => array(
				'Role' => array(
					'ministry_id' => 3,
					'name' => 'My New Role'
				)
			)
		));
		$countAfter = $this->Roles->Role->find('count');
		$this->assertEqual($countBefore, $countAfter-1);
	}

	function testEdit() {
		$this->testAction('/roles/edit/1', array(
			'data' => array(
				'Role' => array(
					'id' => 1,
					'ministry_id' => 3
				)
			)
		));
		$results = $this->Roles->Role->read(null, 1);
		$this->assertEqual($results['Role']['id'], 1);
		$this->assertEqual($results['Role']['ministry_id'], 3);
	}

	function testDelete() {
		$this->testAction('/roles/delete/1');
		$this->assertFalse($this->Roles->Role->read(null, 1));
	}

}
