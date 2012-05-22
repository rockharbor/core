<?php
/* Dates Test cases generated on: 2010-07-12 09:07:14 : 1278951854 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail.QueueEmail'));
App::import('Controller', 'Dates');

Mock::generatePartial('QueueEmailComponent', 'MockDatesQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('DatesController', 'TestDatesController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class DatesControllerTestCase extends CoreTestCase {
	
	function startTest($method) {
		parent::startTest($method);
		Router::parseExtensions('json');
		
		$this->loadFixtures('Involvement', 'Date');
		$this->Dates =& new TestDatesController();
		$this->Dates->__construct();
		$this->Dates->constructClasses();
		$this->Dates->Notifier->QueueEmail = new MockDatesQueueEmailComponent();
		$this->Dates->Notifier->QueueEmail->enabled = true;
		$this->Dates->Notifier->QueueEmail->initialize($this->Dates);
		$this->Dates->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Dates->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->Dates->setReturnValue('isAuthorized', true);
		$this->testController = $this->Dates;
	}

	function endTest() {
		unset($this->Dates);		
		ClassRegistry::flush();
	}

	function testCalendar() {
		$this->loadFixtures('Roster', 'Leader', 'Ministry');
		
		$vars = $this->testAction('/dates/calendar/full.json', array(
			'return' => 'vars',
			'method' => 'get',
			'data' => array(
				'start' => strtotime('5/1/2010'),
				'end' => strtotime('6/30/2010')
			)
		));
		$results = Set::extract('/Involvement/id', $vars['events']);
		$expected = array(4);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/dates/calendar/full.json', array(
			'return' => 'vars',
			'method' => 'get',
			'data' => array(
				'start' => strtotime('1/1/2010'),
				'end' => strtotime('1/1/2011')
			)
		));
		$results = Set::extract('/Involvement/id', $vars['events']);
		$expected = array(1, 4, 5, 6);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/dates/calendar/User:2.json', array(
			'return' => 'vars',
			'method' => 'get',
			'data' => array(
				'start' => strtotime('1/1/2010'),
				'end' => strtotime('1/1/2011')
			)
		));
		$results = Set::extract('/Involvement/id', $vars['events']);
		$expected = array(1);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/dates/calendar/Involvement:5/full.json', array(
			'return' => 'vars',
			'method' => 'get',
			'data' => array(
				'start' => strtotime('1/1/2010'),
				'end' => strtotime('1/1/2011')
			)
		));
		$results = Set::extract('/Involvement/id', $vars['events']);
		$expected = array(5);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/dates/calendar/User:2/Involvement:5/full.json', array(
			'return' => 'vars',
			'method' => 'get',
			'data' => array(
				'start' => strtotime('1/1/2010'),
				'end' => strtotime('1/1/2011')
			)
		));
		$results = Set::extract('/Involvement/id', $vars['events']);
		$expected = array();
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/dates/calendar/User:5/Involvement:5/full.json', array(
			'return' => 'vars',
			'method' => 'get',
			'data' => array(
				'start' => strtotime('1/1/2010'),
				'end' => strtotime('1/1/2011')
			)
		));
		$results = Set::extract('/Involvement/id', $vars['events']);
		$expected = array(5);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/dates/calendar/Involvement:1,4/full.json', array(
			'return' => 'vars',
			'method' => 'get',
			'data' => array(
				'start' => strtotime('1/1/2010'),
				'end' => strtotime('1/1/2011')
			)
		));
		$results = Set::extract('/Involvement/id', $vars['events']);
		$expected = array(1, 4);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/dates/calendar/Involvement:1,4/Ministry:4,1/full.json', array(
			'return' => 'vars',
			'method' => 'get',
			'data' => array(
				'start' => strtotime('1/1/2010'),
				'end' => strtotime('1/1/2011')
			)
		));
		$results = Set::extract('/Involvement/id', $vars['events']);
		$expected = array(1, 4, 5);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/dates/calendar/Campus:1/full.json', array(
			'return' => 'vars',
			'method' => 'get',
			'data' => array(
				'start' => strtotime('1/1/2009'),
				'end' => strtotime('1/1/2012')
			)
		));
		$results = Set::extract('/Involvement/id', $vars['events']);
		$expected = array(1, 4, 5);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/dates/calendar/Campus:2/full.json', array(
			'return' => 'vars',
			'method' => 'get',
			'data' => array(
				'start' => strtotime('1/1/2009'),
				'end' => strtotime('1/1/2012')
			)
		));
		$results = Set::extract('/Involvement/id', $vars['events']);
		$expected = array(6);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/dates/calendar/User:4/full.json', array(
			'return' => 'vars',
			'method' => 'get',
			'data' => array(
				'start' => strtotime('1/1/2010'),
				'end' => strtotime('1/1/2011')
			)
		));
		$results = Set::extract('/Involvement/id', $vars['events']);
		$expected = array();
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