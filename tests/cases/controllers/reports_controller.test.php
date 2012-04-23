<?php
/* Reports Test cases generated on: 2010-07-19 12:07:49 : 1279566109 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail.QueueEmail', 'RequestHandler'));
App::import('Controller', 'Reports');

Mock::generatePartial('QueueEmailComponent', 'MockReportsQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('RequestHandlerComponent', 'MockReportsRequestHandlerComponent', array('_header'));
Mock::generatePartial('ReportsController', 'TestReportsController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));


class ReportsControllerTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		Router::parseExtensions('csv', 'print');
		
		// necessary fixtures
		$this->loadFixtures('User', 'Roster', 'Ministry', 'Involvement', 'Campus', 'InvolvementType');
		$this->Reports = new TestReportsController();
		$this->Reports->__construct();
		$this->Reports->constructClasses();
		$this->Reports->Notifier->QueueEmail = new MockReportsQueueEmailComponent();
		$this->Reports->Notifier->QueueEmail->enabled = true;
		$this->Reports->Notifier->QueueEmail->initialize($this->Reports);
		$this->Reports->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Reports->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->Reports->setReturnValue('isAuthorized', true);

		$this->testController = $this->Reports;
	}

	function endTest() {
		unset($this->Reports);
		ClassRegistry::flush();
	}

	function testIndex() {
		$vars = $this->testAction('/reports/index');
		$results = $vars['userCounts']['involved'];
		$this->assertEqual($results, 4);
		$results = $vars['ministryCounts']['active'];
		$this->assertEqual($results, 3);
		$results = $vars['involvementCounts']['Event']['involved'];
		$this->assertEqual($results, 3);
		$results = $vars['involvementCounts']['Group']['total'];
		$this->assertEqual($results, 3);

		$vars = $this->testAction('/reports/index', array(
			'data' => array(
				'Ministry' => array(
					'id' => 2
				)
			)
		));
		$results = $vars['userCounts']['involved'];
		$this->assertEqual($results, 0);
		$results = $vars['involvementCounts']['Group']['total'];
		$this->assertEqual($results, 0);
		$results = $vars['ministryCounts']['active'];
		$this->assertEqual($results, 1);
	}

	function testPayments() {
		$this->loadFixtures('Payment', 'PaymentOption', 'PaymentType');

		$vars = $this->testAction('/reports/payments');
		$this->assertNotEqual(count($vars['payments']), 0);

		$vars = $this->testAction('/reports/payments', array(
			'data' => array(
				'Involvement' => array(
					'name' => 'testing'
				)
			)
		));
		$this->assertEqual(count($vars['payments']), 0);

		$vars = $this->testAction('/reports/payments', array(
			'data' => array(
				'Involvement' => array(
					'name' => 'CORE'
				)
			)
		));
		$results = Set::extract('/Payment/id', $vars['payments']);
		$expected = array(1, 2, 3);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/reports/payments', array(
			'data' => array(
				'PaymentOption' => array(
					'account_code' => '456'
				)
			)
		));
		$results = Set::extract('/Payment/id', $vars['payments']);
		$expected = array(1, 2, 3, 6);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/reports/payments', array(
			'data' => array(
				'PaymentOption' => array(
					'account_code' => '456'
				),
				'PaymentType' => array(
					'id' => 2
				)
			)
		));
		$results = Set::extract('/Payment/id', $vars['payments']);
		$expected = array(3, 6);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/reports/payments', array(
			'data' => array(
				'PaymentOption' => array(
					'account_code' => '456'
				),
				'Payment' => array(
					'start_date' => '5/6/2010',
					'end_date' => '5/7/2010'
				),
				'PaymentType' => array(
					'id' => 2
				)
			)
		));
		$results = Set::extract('/Payment/id', $vars['payments']);
		$expected = array(3);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/reports/payments', array(
			'data' => array(
				'Ministry' => array(
					'id' => 4
				),
				'PaymentOption' => array(
					'account_code' => '456'
				),
				'PaymentType' => array(
					'id' => 2
				)
			)
		));
		$results = Set::extract('/Payment/id', $vars['payments']);
		$expected = array(3);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/reports/payments', array(
			'data' => array(
				'PaymentType' => array(
					'type' => 1
				)
			)
		));
		$results = Set::extract('/Payment/id', $vars['payments']);
		$expected = array(3, 6);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/reports/payments', array(
			'data' => array(
				'PaymentType' => array(
					'type' => 0
				)
			)
		));
		$results = Set::extract('/Payment/id', $vars['payments']);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);
	}
	
	function testInvolvementMap() {
		$this->loadFixtures('Address');
		
		$vars = $this->testAction('/reports/involvement_map/Involvement:1');
		$results = Set::extract('/Address/name', $vars['results']);
		sort($results);
		$expected = array(
			'Central Mini-lab 1'
		);
		$this->assertEqual($results, $expected);
	}
	
	function testUserMap() {
		$this->loadFixtures('Profile', 'Address');
		
		$this->Reports->Session->write('MultiSelect.testMap', array(
			'selected' => array(1),
			'search' => array(
				'conditions' => array(
					'User.username' => 'jharris'
				)
			)
		));
		$vars = $this->testAction('/reports/user_map/User/testMap');
		$results = Set::extract('/Profile/name', $vars['results']);
		$expected = array('Jeremy Harris');
		$this->assertEqual($results, $expected);

		$this->Reports->Session->write('MultiSelect.testMap', array(
			'selected' => array(1, 3),
			'search' => array()
		));
		$vars = $this->testAction('/reports/user_map/Roster/testMap');
		$results = Set::extract('/User/id', $vars['results']);
		sort($results);
		$expected = array(1, 3);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/reports/user_map/User/User:2');
		$results = Set::extract('/User/username', $vars['results']);
		$expected = array('rickyrockharbor');
		$this->assertEqual($results, $expected);
	}

	function testExportCsvWithSearch() {
		$this->Reports->RequestHandler = new MockReportsRequestHandlerComponent();
		
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
				'squashed_fields' => '',
				'Ministry' => array(
					'name' => 1
				)
			)
		);
		
		$vars = $this->testAction('/reports/export/Ministry/testExportCsvWithSearch.csv', array(
			'data' => $data
		));
		
		$results = Set::extract('/Ministry/name', $vars['results']);
		sort($results);
		$expected = array('All Church', 'Alpha', 'Communications');
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
				'squashed_fields' => '',
				'Ministry' => array(
					'name' => 1
				)
			)
		);
		$vars = $this->testAction('/reports/export/Ministry/testExportPrint.print', array(
			'data' => $data
		));
		$results = $vars['models'];
		$expected = array(
			'Ministry' => array(
				'name' => 1
		));
		$this->assertEqual($results, $expected);

		$results = Set::extract('/Ministry/name', $vars['results']);
		sort($results);
		$expected = array('All Church', 'Alpha');
		$this->assertEqual($results, $expected);
	}

/**
 * Tests exporting extra fields that weren't in the original conditions
 */
	function testExportExtraFields() {
		$this->loadFixtures('Profile');
		$this->Reports->Session->write('MultiSelect.testExportExtraFields', array(
			'selected' => array(1, 2),
			'search' => array(
				'conditions' => array(),
				'link' => array(
					'User' => array(
						'Profile' => array(
							'fields' => array('name', 'user_id')
						)
					)
				)
			)
		));
		$data = array(
			'Export' => array(
				'type' => 'print',
				'header_aliases' => '',
				'squashed_fields' => '',
				'User' => array(
					'Profile' => array(
						'name' => 1,
						'first_name' => 1
					)
				)
			)
		);
		$vars = $this->testAction('/reports/export/Roster/testExportExtraFields.print', array(
			'data' => $data
		));
		$results = Set::extract('/User/Profile/first_name', $vars['results']);
		sort($results);
		$expected = array('ricky', 'ricky jr.');
		$this->assertEqual($results, $expected);
	}

}
?>