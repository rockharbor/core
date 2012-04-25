<?php
App::import('Lib', 'CoreTestCase');
App::import('Controller', 'Pages');

Mock::generatePartial('PagesController', 'TestPagesController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class PagesControllerTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		Router::parseExtensions('json');
		
		$this->loadFixtures('Ministry', 'Involvement');
		$this->Pages =& new TestPagesController();
		$this->Pages->__construct();
		$this->Pages->constructClasses();
		$this->testController = $this->Pages;
	}

	function endTest() {
		unset($this->Pages);
		ClassRegistry::flush();
	}
	
	function testDisplay() {
		$vars = $this->testAction('/pages/display/test');
		
		$result = $vars['page'];
		$expected = 'test';
		$this->assertEqual($result, $expected);
		
		$result = $vars['title_for_layout'];
		$expected = 'Test';
		$this->assertEqual($result, $expected);
	}

	function testPhrase() {
		$vars = $this->testAction('/pages/phrase/1.json', array(
			'return' => 'vars'
		));
		$this->assertTrue(in_array($vars['model'], array('Involvement', 'Ministry')));
		$Model = ClassRegistry::init($vars['model']);
		$this->assertTrue($Model->exists($vars['result'][$vars['model']]['id']));
		$this->assertTrue($vars['result'][$vars['model']]['active']);
	}

}

?>
