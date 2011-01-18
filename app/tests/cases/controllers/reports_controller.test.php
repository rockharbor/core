<?php
/* Reports Test cases generated on: 2010-07-19 12:07:49 : 1279566109 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('RequestHandler', 'QueueEmail.QueueEmail', 'Notifier'));
App::import('Controller', 'Reports');

Mock::generatePartial('QueueEmailComponent', 'MockQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('NotifierComponent', 'MockNotifierComponent', array('_render'));
Mock::generatePartial('RequestHandlerComponent', 'MockRequestHandlerComponent', array('_header'));
Mock::generatePartial('ReportsController', 'TestReportsController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class ReportsControllerTestCase extends CoreTestCase {

	function startTest() {
		// necessary fixtures
		$this->loadFixtures('User', 'Roster', 'Ministry', 'Involvement', 'Campus');
		$this->Reports = new TestReportsController();
		$this->Reports->__construct();
		$this->Reports->constructClasses();
		$this->Reports->Notifier = new MockNotifierComponent();
		$this->Reports->Notifier->initialize($this->Reports);
		$this->Reports->Notifier->setReturnValue('_render', 'Notification body text');
		$this->Reports->Notifier->QueueEmail = new MockQueueEmailComponent();
		$this->Reports->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Reports->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->Reports->RequestHandler = new MockRequestHandlerComponent();
		$this->Reports->setReturnValue('isAuthorized', true);

		$this->testController = $this->Reports;
	}

	function endTest() {
		unset($this->Reports);
		ClassRegistry::flush();
	}

	function testMinistry() {
		$vars = $this->testAction('/reports/ministry');
		$results = count($vars['ministries']);
		$this->assertEqual($results, 4);

		$vars = $this->testAction('/reports/ministry', array(
			'data' => array(
				'Ministry' => array(
					'id' => 2
				)
			)
		));
		$results = Set::extract('/Ministry/name', $vars['ministries']);
		$expected = array(
			'Alpha'
		);
		$this->assertEqual($results, $expected);
	}

	function testMap() {
		$this->loadFixtures('Profile');
		
		$this->Reports->Session->write('MultiSelect.testMap', array(
			'selected' => array(1),
			'search' => array(
				'conditions' => array(
					'User.username' => 'jharris'
				)
			)
		));
		$vars = $this->testAction('/reports/map/User/testMap');
		$results = Set::extract('/Profile/name', $vars['results']);
		$expected = array('Jeremy Harris');
		$this->assertEqual($results, $expected);

		$this->Reports->Session->write('MultiSelect.testMap', array(
			'selected' => array(1),
			'search' => array()
		));
		$vars = $this->testAction('/reports/map/Involvement/testMap');
		$results = Set::extract('/Involvement/name', $vars['results']);
		$expected = array('CORE 2.0 testing');
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/reports/map/User/User:2');
		$results = Set::extract('/User/username', $vars['results']);
		$expected = array('rickyrockharbor');
		$this->assertEqual($results, $expected);
	}

	function testExportCsvWithSearch() {
		$this->Reports->Session->write('MultiSelect.testExportCsvWithSearch', array(
			'selected' => array(),
		   'search' => array(
				'conditions' => array(
					'Ministry.parent_id' => null
				)
			)
		));
		$data = array(
			'Export' => array(
				'type' => 'csv',
				'header_aliases' => '',
				'Ministry' => array(
					'name'
				)
			)
		);
		
		$this->Reports->RequestHandler->expectAt(0, '_header', array('Content-Disposition: attachment; filename="ministry-search-export.csv"'));
		$this->Reports->RequestHandler->expectAt(1, '_header', array('Content-Type: application/vnd.ms-excel; charset=UTF-8'));	
		$vars = $this->testAction('/reports/export/Ministry/testExportCsvWithSearch.csv', array(
			'data' => $data
		));
		
		$results = Set::extract('/Ministry/name', $vars['results']);
		$expected = array('Communications', 'Alpha', 'All Church');
		$this->assertEqual($results, $expected);
	}

	function testExportPrint() {
		$this->Reports->Session->write('MultiSelect.testExportPrint', array(
			'selected' => array(2,3),
			'search' => array(
				'conditions' => array()
			)
		));
		$data = array(
			'Export' => array(
				'type' => 'print',
				'header_aliases' => '',
				'Ministry' => array(
					'name'
				)
			)
		);
		$this->Reports->RequestHandler->expectAt(0, '_header', array('Content-Type: text/html; charset=UTF-8'));
		$vars = $this->testAction('/reports/export/Ministry/testExportPrint.print', array(
			'data' => $data
		));
		$results = $vars['models'];
		$expected = array(
			'Ministry' => array(
				'name'
		));
		$this->assertEqual($results, $expected);

		$results = Set::extract('/Ministry/name', $vars['results']);
		$expected = array('Alpha', 'All Church');
		$this->assertEqual($results, $expected);
	}

}
?>