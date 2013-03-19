<?php
App::uses('CoreTestCase', 'Lib');
App::uses('PagesController', 'Controller');

Mock::generatePartial('PagesController', 'TestPagesController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class PagesControllerTestCase extends CoreTestCase {

	public function startTest($method) {
		parent::startTest($method);
		Router::parseExtensions('json');

		$this->Pages =& new TestPagesController();
		$this->Pages->__construct();
		$this->Pages->constructClasses();
		$this->testController = $this->Pages;
	}

	public function endTest() {
		unset($this->Pages);
		ClassRegistry::flush();
	}

	public function testDisplay() {
		$vars = $this->testAction('/pages/display/test');

		$result = $vars['page'];
		$expected = 'test';
		$this->assertEqual($result, $expected);

		$result = $vars['title_for_layout'];
		$expected = 'Test';
		$this->assertEqual($result, $expected);
	}

	public function testPhrase() {
		$this->loadFixtures('Ministry', 'Involvement');

		$vars = $this->testAction('/pages/phrase/Involvement.json', array(
			'return' => 'vars'
		));
		$this->assertEqual($vars['model'], 'Involvement');
		$Model = ClassRegistry::init($vars['model']);
		$this->assertTrue($Model->exists($vars['result'][$vars['model']]['id']));
		$this->assertTrue($vars['result'][$vars['model']]['active']);
		$this->assertFalse($vars['result'][$vars['model']]['private']);
		$this->assertFalse($vars['result'][$vars['model']]['previous']);

		$vars = $this->testAction('/pages/phrase/Ministry.json', array(
			'return' => 'vars'
		));
		$this->assertEqual($vars['model'], 'Ministry');
		$Model = ClassRegistry::init($vars['model']);
		$this->assertTrue($Model->exists($vars['result'][$vars['model']]['id']));
		$this->assertTrue($vars['result'][$vars['model']]['active']);
		$this->assertFalse($vars['result'][$vars['model']]['private']);

		$this->assertNoErrors();
		$vars = $this->testAction('/pages/phrase.json', array(
			'return' => 'vars'
		));
	}

	public function testPhraseNoRecords() {
		$this->assertNoErrors();
		$vars = $this->testAction('/pages/phrase/Involvement.json', array(
			'return' => 'vars'
		));
		$this->assertEqual($vars['model'], null);
	}

}

