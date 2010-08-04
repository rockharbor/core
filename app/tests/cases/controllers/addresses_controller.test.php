<?php
/* Addresses Test cases generated on: 2010-07-02 11:07:49 : 1278096229 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail'));
App::import('Controller', 'UserAddresses');

Mock::generate('QueueEmailComponent');
Mock::generatePartial('UserAddressesController', 'TestUserAddressesController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class AddressesControllerTestCase extends CoreTestCase {

	var $fixtures = array('app.ministries_rev', 'app.involvements_rev', 'app.alert', 'app.group', 'app.user', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.roster', 'app.role', 'app.roster_status', 'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member', 'app.household', 'app.publication', 'app.publications_user', 'app.alerts_user', 'app.log', 'app.app_setting');

	var $autoFixtures = false;
	
	function startTest() {
		$this->loadFixtures('Address');
		$this->Addresses =& new TestUserAddressesController();
		$this->Addresses->constructClasses();
		$this->Addresses->QueueEmail = new MockQueueEmailComponent();
		$this->Addresses->setReturnValue('isAuthorized', true);
		$this->Addresses->QueueEmail->setReturnValue('send', true);
		$this->testController = $this->Addresses;
	}

	function endTest() {
		$this->Addresses->Session->destroy();
		unset($this->Addresses);
		ClassRegistry::flush();
	}

	function testIndex() {
		$vars = $this->testAction('/user_addresses/index/User:1');
		$results = $vars['data'];
		$expected = array(
			array(
				'Address' => array(
					'id' => 1,
					'name' => 'Work',
					'address_line_1' => '3080 Airway',
					'address_line_2' => '',
					'city' => 'Costa Mesa',
					'state' => 'CA',
					'zip' => 92886,
					'lat' => 33.6732979,
					'lng' => -117.8743896,
					'created' => '2010-02-24 09:55:30',
					'modified' => '2010-04-05 10:25:58',
					'foreign_key' => 1,
					'model' => 'User',
					'primary' => 0,
					'active' => 0
				)
			),
			array(
				'Address' => array(
					'id' => 2,
					'name' => 'Home',
					'address_line_1' => '445 S. Pixley St.',
					'address_line_2' => '',
					'city' => 'Orange',
					'state' => 'CA',
					'zip' => 92868,
					'lat' => 33.7815781,
					'lng' => -117.8585281,
					'created' => '2010-02-24 10:52:16',
					'modified' => '2010-06-07 08:34:48',
					'foreign_key' => 1,
					'model' => 'User',
					'primary' => 1,
					'active' => 1
				)
			)
		);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/user_addresses/index/User:2');
		$result = $vars['data'];
		$this->assertEqual($result, array());
	}

	function testAdd() {
		$data = array(
			'Address' => array(
				'name' => 'Work 2',
				'address_line_1' => '3080 Airway',
				'address_line_2' => '',
				'city' => 'Costa Mesa',
				'state' => 'CA',
				'zip' => 92886,
				'model' => 'User',
				'foreign_key' => 1
			)
		);
		$vars = $this->testAction('/user_addresses/add', array(
			'data' => $data
		));
		$this->assertEqual($this->Addresses->Address->field('name'), 'Work 2');
		$this->assertEqual($this->Addresses->Address->field('primary'), 1);
		$this->Addresses->Address->id = 1;
		$this->assertEqual($this->Addresses->Address->field('primary'), 0);
		$this->Addresses->Address->id = 2;
		$this->assertEqual($this->Addresses->Address->field('primary'), 0);
	}

	function testEdit() {
		$data = $this->Addresses->Address->read(null, 1);
		$data['Address']['primary'] = 0;
		$vars = $this->testAction('/user_addresses/edit/1', array(
			'data' => $data
		));		
		$this->Addresses->Address->id = 1;
		$this->assertEqual($this->Addresses->Address->field('primary'), 0);
		$this->Addresses->Address->id = 2;
		$this->assertEqual($this->Addresses->Address->field('primary'), 1);

		$data['Address']['primary'] = 1;
		$vars = $this->testAction('/user_addresses/edit/1', array(
			'data' => $data
		));
		$this->Addresses->Address->id = 1;
		$this->assertEqual($this->Addresses->Address->field('primary'), 1);
		$this->Addresses->Address->id = 2;
		$this->assertEqual($this->Addresses->Address->field('primary'), 0);
	}

	function testDelete() {
		$vars = $this->testAction('/user_addresses/delete/1');
		$this->assertFalse($this->Addresses->Address->read(null, 1));
	}

}
?>