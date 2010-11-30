<?php
/* Dates Test cases generated on: 2010-07-12 09:07:14 : 1278951854 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail.QueueEmail', 'Notifier', 'RequestHandler'));
App::import('Controller', 'Dates');

Mock::generatePartial('QueueEmailComponent', 'MockQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('NotifierComponent', 'MockNotifierComponent', array('_render'));
Mock::generatePartial('DatesController', 'TestDatesController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));
Mock::generatePartial('RequestHandlerComponent', 'MockRequestHandlerComponent', array('_header'));

class DatesControllerTestCase extends CoreTestCase {
	
	function startTest() {
		$this->loadFixtures('Involvement', 'Date');
		$this->Dates =& new TestDatesController();
		$this->Dates->__construct();
		$this->Dates->constructClasses();
		$this->Dates->Notifier = new MockNotifierComponent();
		$this->Dates->Notifier->initialize($this->Dates);
		$this->Dates->Notifier->setReturnValue('_render', 'Notification body text');
		$this->Dates->Notifier->QueueEmail = new MockQueueEmailComponent();
		$this->Dates->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Dates->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->Dates->RequestHandler = new MockRequestHandlerComponent();
		$this->Dates->setReturnValue('isAuthorized', true);
		$this->testController = $this->Dates;
	}

	function endTest() {
		unset($this->Dates);		
		ClassRegistry::flush();
	}

	function testCalendar() {
		$vars = $this->testAction('/dates/calendar.json', array(
			'return' => 'vars',
			'method' => 'get',
			'data' => array(
				'start' => strtotime('5/1/2010'),
				'end' => strtotime('5/30/2010')
			)
		));
		$results = Set::extract('/Involvement', $vars['events']);
		$expected = array(
			array(
				'Involvement' => array(
					'id' => 2,
					'name' => 'Third Wednesday'					
				)
			)
		);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/dates/calendar/passed.json', array(
			'return' => 'vars',
			'method' => 'get',
			'data' => array(
				'start' => strtotime('4/1/2010'),
				'end' => strtotime('4/30/2010')
			)
		));
		$results = Set::extract('/Involvement', $vars['events']);
		$expected = array(
			array(
				'Involvement' => array(
					'id' => 1,
					'name' => 'CORE 2.0 testing'
				)
			),
			array(
				'Involvement' => array(
					'id' => 2,
					'name' => 'Third Wednesday'
				)
			),
			array(
				'Involvement' => array(
					'id' => 3,
					'name' => 'Team CORE'
				)
			),
			array(
				'Involvement' => array(
					'id' => 4,
					'name' => 'Rock Climbing'
				)
			)
		);
		$this->assertEqual($results, $expected);
	}

	function testIndex() {
		$vars = $this->testAction('/dates/index/Involvement:2', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Date/id', $vars['dates']);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);
	}

	function testAdd() {
		$data = array(
			'Date' => array(
				'start_date' => '2010-03-16',
				'end_date' => '2010-03-16',
				'start_time' => '07:05:00',
				'end_time' => '10:00:00',
				'all_day' => 1,
				'permanent' => 0,
				'recurring' => 0,
				'recurrance_type' => 'mw',
				'frequency' => 1,
				'weekday' => 0,
				'day' => 0,
				'involvement_id' => 2,
				'exemption' => 1,
				'offset' => 0
			)
		);
		$this->testAction('/dates/add/Involvement:2', array(
			'data' => $data
		));
		$this->Dates->Date->id = 12;
		$date = $this->Dates->Date->read();
		$this->assertEqual($date['Date']['start_time'], '00:00:00');
		$this->assertEqual($date['Date']['end_time'], '23:59:00');
	}

	function testEdit() {
		$data = array(
			'Date' => array(
				'id' => 1,
				'end_date' => '2010-03-18',
				'permanent' => 0
			)
		);
		$this->testAction('/dates/edit/1', array(
			'data' => $data
		));
		$this->Dates->Date->id = 1;
		$date = $this->Dates->Date->read();
		$this->assertNotEqual($date['Date']['modified'], '2010-03-16 13:32:48');
	}

	function testDelete() {
		$this->testAction('/dates/delete/1');
		$result = $this->Dates->Date->read(null, 1);
		$this->assertFalse($result);
	}

}
?>