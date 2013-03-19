<?php
/* Reports Test cases generated on: 2010-07-19 12:07:49 : 1279566109 */
App::uses('CoreTestCase', 'Lib');
App::uses('QueueEmailComponent', 'QueueEmail.Controller/Component');
App::uses('RequestHandlerComponent', 'Controller/Component');
App::uses('ReportsController', 'Controller');

Mock::generatePartial('QueueEmailComponent', 'MockReportsQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('RequestHandlerComponent', 'MockReportsRequestHandlerComponent', array('_header'));
Mock::generatePartial('ReportsController', 'TestReportsController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));


class ReportsControllerTestCase extends CoreTestCase {

	public function startTest($method) {
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

	public function endTest() {
		unset($this->Reports);
		ClassRegistry::flush();
	}

	public function testIndex() {
		$this->loadFixtures('Date');

		$vars = $this->testAction('/reports/index');
		$results = $vars['userCounts']['involved'];
		$this->assertEqual($results, 5);
		$results = $vars['ministryCounts']['active'];
		$this->assertEqual($results, 3);
		$results = $vars['involvementCounts']['Event']['involved'];
		$this->assertEqual($results, 1);
		$results = $vars['involvementCounts']['Event']['total'];
		$this->assertEqual($results, 1);
		$results = $vars['involvementCounts']['Group']['total'];
		$this->assertEqual($results, 1);

		$vars = $this->testAction('/reports/index', array(
			'data' => array(
				'Ministry' => array(
					'campus_id' => null,
					'id' => null
				),
				'Involvement' => array(
					'previous' => 'both'
				)
			)
		));
		$results = $vars['involvementCounts']['Group']['total'];
		$this->assertEqual($results, 3);

		$vars = $this->testAction('/reports/index', array(
			'data' => array(
				'Ministry' => array(
					'campus_id' => null,
					'id' => null
				),
				'Involvement' => array(
					'previous' => 'previous'
				)
			)
		));
		$results = $vars['involvementCounts']['Group']['total'];
		$this->assertEqual($results, 2);

		$vars = $this->testAction('/reports/index', array(
			'data' => array(
				'Ministry' => array(
					'id' => 2
				),
				'Involvement' => array(
					'previous' => 'current'
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

	public function testPayments() {
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

	public function testInvolvementMap() {
		$this->loadFixtures('Address');

		$vars = $this->testAction('/reports/involvement_map/Involvement:1');
		$results = Set::extract('/Address/name', $vars['results']);
		sort($results);
		$expected = array(
			'Central Mini-lab 1'
		);
		$this->assertEqual($results, $expected);
	}

	public function testUserMap() {
		$this->loadFixtures('Profile', 'Address');

		$this->Reports->Session->write('MultiSelect.testMap', array(
			'selected' => array(1),
			'search' => array(
				'conditions' => array(
					'User.username' => 'jharris'
				)
			)
		));
		$vars = $this->testAction('/reports/user_map/User/mstoken:testMap');
		$results = Set::extract('/Profile/name', $vars['results']);
		$expected = array('Jeremy Harris');
		$this->assertEqual($results, $expected);

		$this->Reports->Session->write('MultiSelect.testMap', array(
			'selected' => array(1, 3),
			'search' => array()
		));
		$vars = $this->testAction('/reports/user_map/Roster/mstoken:testMap');
		$results = Set::extract('/User/id', $vars['results']);
		sort($results);
		$expected = array(1, 3);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/reports/user_map/User/User:2');
		$results = Set::extract('/User/username', $vars['results']);
		$expected = array('rickyrockharbor');
		$this->assertEqual($results, $expected);
	}

	public function testUserMapMissingCoords() {
		$this->loadFixtures('Address');
		$floatReg = '/^[+-]?(([0-9]+)|([0-9]*\.[0-9]+|[0-9]+\.[0-9]*)|(([0-9]+|([0-9]*\.[0-9]+|[0-9]+\.[0-9]*))[eE][+-]?[0-9]+))$/';

		$this->Reports->Session->write('MultiSelect.test', array(
			'selected' => array(2, 3),
			'search' => array()
		));

		$vars = $this->testAction('/reports/user_map/User/test');

		foreach ($vars['results'] as $result) {
			$addresses = $result['Address'];
			foreach ($addresses as $address) {
				if (!empty($address['id'])) {
					$this->assertNotEqual(0.0000000, $address['lat']);
					$this->assertNotEqual(0.0000000, $address['lng']);
					$this->assertPattern($floatReg, $address['lat']);
					$this->assertPattern($floatReg, $address['lng']);
				}
			}

			$address = $result['ActiveAddress'];
			if (!empty($result['ActiveAddress']['id'])) {
				$this->assertNotEqual(0.0000000, $address['lat']);
				$this->assertNotEqual(0.0000000, $address['lng']);
				$this->assertPattern($floatReg, $address['lat']);
				$this->assertPattern($floatReg, $address['lng']);
			}
		}

		// ensure the new data was saved
		$address = ClassRegistry::init('Address')->read(null, 4);
		$this->assertNotEqual(0.0000000, $address['Address']['lat']);
		$this->assertNotEqual(0.0000000, $address['Address']['lng']);
		$this->assertPattern($floatReg, $address['Address']['lat']);
		$this->assertPattern($floatReg, $address['Address']['lng']);
	}

	public function testExportCsvWithSearch() {
		$this->Reports->RequestHandler = new MockReportsRequestHandlerComponent();

		$this->Reports->Session->write('MultiSelect.testExportCsvWithSearch', array(
			'selected' => array(),
			'search' => array(
				'conditions' => array(
					'Ministry.parent_id' => null
				)
			),
			'all' => true
		));
		$data = array(
			'Export' => array(
				'type' => 'csv',
				'header_aliases' => '',
				'squashed_fields' => '',
				'multiple_records' => '',
				'Ministry' => array(
					'name' => 1
				)
			)
		);

		$vars = $this->testAction('/reports/export/Ministry/mstoken:testExportCsvWithSearch.csv', array(
			'data' => $data
		));

		$results = Set::extract('/Ministry/name', $vars['results']);
		sort($results);
		$expected = array('All Church', 'Alpha', 'Communications');
		$this->assertEqual($results, $expected);
	}

	public function testExportPrint() {
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
				'multiple_records' => '',
				'Ministry' => array(
					'name' => 1
				)
			)
		);
		$vars = $this->testAction('/reports/export/Ministry/mstoken:testExportPrint.print', array(
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
	public function testExportExtraFields() {
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
				'multiple_records' => '',
				'User' => array(
					'Profile' => array(
						'name' => 1,
						'first_name' => 1
					)
				)
			)
		);
		$vars = $this->testAction('/reports/export/Roster/mstoken:testExportExtraFields.print', array(
			'data' => $data
		));
		$results = Set::extract('/User/Profile/first_name', $vars['results']);
		sort($results);
		$expected = array('ricky', 'ricky jr.');
		$this->assertEqual($results, $expected);
	}

}
