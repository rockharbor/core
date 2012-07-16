<?php
App::import('Lib', 'CoreTestCase');
App::import('Controller', 'Questions');

class TestQuestionsController extends QuestionsController {
	
	function redirect($url, $status = null, $exit = true) {
		if (is_array($url)) {
			$url += array('controller' => 'questions');
		}
		$this->redirectUrl = Router::url($url);
	}
	
}

Mock::generatePartial('TestQuestionsController', 'MockTestQuestionsController', array('isAuthorized', 'disableCache', 'render', '_stop', 'header', 'cakeError'));

class QuestionsControllerTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		// necessary fixtures
		$this->loadFixtures('Question');
		$this->Questions =& new MockTestQuestionsController();
		$this->Questions->__construct();
		$this->Questions->constructClasses();
		$this->Questions->setReturnValue('isAuthorized', true);
		$this->testController = $this->Questions;
	}

	function endTest() {
		$this->Questions->Session->destroy();
		unset($this->Questions);
		ClassRegistry::flush();
	}
	
	function testIndex() {
		$vars = $this->testAction('/questions/index/Involvement:1');
		$results = Set::extract('/Question/id', $vars['questions']);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);
	}
	
	function testAdd() {
		$data = array(
			'Question' => array(
				'description' => 'I\'m Ron Burgundy?',
				'involvement_id' => 1
			)
		);
		$vars = $this->testAction('/question/add/Involvement:1', array(
			'data' => $data
		));
		$results = $vars['involvementId'];
		$expected = 1;
		$this->assertEqual($results, $expected);
		$results = $this->Questions->Session->read('Message.flash.element');
		$expected = 'flash'.DS.'success';
		$this->assertEqual($results, $expected);
	}
	
	function testEdit() {
		$data = array(
			'Question' => array(
				'id' => 1,
				'description' => 'I\'m Ron Burgundy?',
				'involvement_id' => 1
			)
		);
		$vars = $this->testAction('/question/edit/1/Involvement:1', array(
			'data' => $data
		));
		$results = $this->Questions->Session->read('Message.flash.element');
		$expected = 'flash'.DS.'success';
		$this->assertEqual($results, $expected);
		$question = $this->Questions->Question->read(null, 1);
		$results = $question['Question']['description'];
		$expected = $data['Question']['description'];
		$this->assertEqual($results, $expected);
	}
	
	function testMove() {
		$this->testAction('/question/move/1/down');
		$results = $this->testController->redirectUrl;
		$expected = '/questions/index/Involvement:1';
		$this->assertEqual($results, $expected);
		$results = $this->Questions->Session->read('Message.flash.element');
		$expected = 'flash'.DS.'success';
		$question = $this->Questions->Question->read(null, 1);
		$results = $question['Question']['order'];
		$expected = 2;
		$this->assertEqual($results, $expected);
		
		$this->testAction('/question/move/1/up');
		$results = $this->testController->redirectUrl;
		$expected = '/questions/index/Involvement:1';
		$this->assertEqual($results, $expected);
		$results = $this->Questions->Session->read('Message.flash.element');
		$expected = 'flash'.DS.'success';
		$question = $this->Questions->Question->read(null, 1);
		$results = $question['Question']['order'];
		$expected = 1;
		$this->assertEqual($results, $expected);
		
		$this->testAction('/question/move/3/down');
		$results = $this->Questions->Session->read('Message.flash.element');
		$expected = 'flash'.DS.'failure';
	}
	
	function testDelete() {
		$this->testAction('/question/delete/1');
		$results = $this->Questions->Session->read('Message.flash.element');
		$expected = 'flash'.DS.'success';
		$question = $this->Questions->Question->read(null, 2);
		$results = $question['Question']['order'];
		$expected = 1;
		$this->assertEqual($results, $expected);
	}

}
