<?php

App::import('Lib', 'CoreTestCase');
App::import('Controller', 'Covenants');

Mock::generatePartial('CovenantsController', 'TestCovenantsController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class CovenantsControllerTestCase extends CoreTestCase {
	
	public function startTest($method) {
		parent::startTest($method);
		
		$this->loadFixtures('User', 'Covenant');
		$this->Covenants =& new TestCovenantsController();
		$this->Covenants->__construct();
		$this->Covenants->constructClasses();
		$this->Covenants->setReturnValue('isAuthorized', true);
		$this->testController = $this->Covenants;
	}
	
	public function endTest() {
		$this->Covenants->Session->destroy();
		unset($this->Covenants);
		ClassRegistry::flush();
	}
	
	public function testIndex() {
		$vars = $this->testAction('/covenants/index/User:1', array(
			'return' => 'vars'
		));
		$result = Set::extract('/Covenant/id', $vars['covenants']);
		$expected = array(1, 2);
		$this->assertEqual($result, $expected);
		
		$vars = $this->testAction('/covenants/index/User:2', array(
			'return' => 'vars'
		));
		$result = Set::extract('/Covenant/id', $vars['covenants']);
		$expected = array(3, 4);
		$this->assertEqual($result, $expected);
	}
	
	public function testAdd() {
		$data = array(
			'Covenant' => array(
				'id' => 5,
				'user_id' => 3,
				'year' => '2014/2015'
			)
		);
		
		$this->testAction('/covenants/add/User:3', array(
			'data' => $data
		));
		$covenant = $this->Covenants->Covenant->read();
		$this->assertEqual($covenant['Covenant']['year'], '2014/2015');
		$this->assertEqual($covenant['Covenant']['user_id'], 3);
		
		$vars = $this->testAction('/covenants/index/User:3', array(
			'return' => 'vars'
		));
		$result = Set::extract('/Covenant/id', $vars['covenants']);
		$expected = array(5);
		$this->assertEqual($result, $expected);
	}
	
	public function testDelete() {
		$this->testAction('/covenants/delete/1');
		$result = $this->Covenants->Covenant->read(null, 1);
		$this->assertFalse($result);
	}
}