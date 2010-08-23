<?php
/* Campuses Test cases generated on: 2010-07-09 14:07:25 : 1278710485 */
App::import('Lib', 'CoreTestCase');
App::import('Component', 'QueueEmail');
App::import('Controller', 'Campuses');

Mock::generate('QueueEmailComponent');
Mock::generatePartial('CampusesController', 'TestCampusesController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class CampusesControllerTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('Campus', 'Ministry', 'Involvement');
		$this->Campuses =& new TestCampusesController();
		$this->Campuses->constructClasses();
		$this->Campuses->Component->initialize($this->Campuses);
		$this->Campuses->QueueEmail = new MockQueueEmailComponent();
		$this->Campuses->setReturnValue('isAuthorized', true);
		$this->Campuses->QueueEmail->setReturnValue('send', true);
		$this->testController = $this->Campuses;
	}

	function endTest() {
		unset($this->Campuses);		
		ClassRegistry::flush();
	}

	function testView() {
		$vars = $this->testAction('/campuses/view/Campus:1', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Campus', $vars['campus']);
		$expected = array(
			array(
				'Campus' => array(
					'id' => 1,
					'name' => 'RH Central',
					'description' => 'The original campus!',
					'active' => 1,
					'created' => '2010-02-08 14:39:06',
					'modified' => '2010-03-11 13:34:41'
				)
			)
		);
		$this->assertEqual($results, $expected);
	}

	function testAdd() {
		$data = array(
			'name' => 'New Campus',
			'description' => 'A slightly newer campus',
			'active' => 1
		);
		$this->testAction('/campuses/add', array(
			'data' => $data
		));
		$count = $this->Campuses->Campus->find('count');
		$this->assertEqual($count, 3);
	}

	function testEdit() {
		$data = array(
			'id' => 1,
			'name' => 'New name'
		);
		$this->testAction('/campuses/edit/Campus:1', array(
			'data' => $data
		));
		$this->Campuses->Campus->id = 1;
		$this->assertEqual($this->Campuses->Campus->field('name'), 'New name');
		$this->assertNotEqual($this->Campuses->Campus->field('modified'), '2010-03-11 13:34:41');
	}

	function testToggleActivity() {
		$this->testAction('/campuses/toggle_activity/1/Campus:2');
		$this->Campuses->Campus->id = 2;
		$this->assertEqual($this->Campuses->Session->read('Message.flash.element'), 'flash'.DS.'failure');

		$data = array(
			'Leader' => array(
				'user_id' => 1,
				'model' => 'Campus',
				'model_id' => 2
			)
		);
		$this->Campuses->Campus->Leader->save($data);
		$this->testAction('/campuses/toggle_activity/1/Campus:2');
		$this->Campuses->Campus->id = 2;
		$this->assertEqual($this->Campuses->Campus->field('active'), 1);
		$this->assertEqual($this->Campuses->Session->read('Message.flash.element'), 'flash'.DS.'success');
	}

	function testDelete() {
		$this->testAction('/campuses/delete/1');
		$this->assertFalse($this->Campuses->Campus->read(null, 1));
		$this->assertFalse($this->Campuses->Campus->Ministry->read(null, 1));
		$this->assertFalse($this->Campuses->Campus->Ministry->Involvement->read(null, 4));
	}

}
?>