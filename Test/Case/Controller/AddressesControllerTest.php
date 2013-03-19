<?php
/* Addresses Test cases generated on: 2010-07-02 11:07:49 : 1278096229 */
App::uses('CoreTestCase', 'Lib');
App::uses('QueueEmailComponent', 'QueueEmail.Controller/Component');
App::uses('UserAddressesController', 'Controller');

Mock::generatePartial('QueueEmailComponent', 'MockAddressesQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('UserAddressesController', 'TestUserAddressesController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class AddressesControllerTestCase extends CoreTestCase {

	public function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Address');
		$this->Addresses =& new TestUserAddressesController();
		$this->Addresses->__construct();
		$this->Addresses->constructClasses();
		$this->Addresses->Notifier->QueueEmail = new MockAddressesQueueEmailComponent();
		$this->Addresses->Notifier->QueueEmail->enabled = true;
		$this->Addresses->Notifier->QueueEmail->initialize($this->Addresses);
		$this->Addresses->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Addresses->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->Addresses->setReturnValue('isAuthorized', true);
		$this->testController = $this->Addresses;
	}

	public function endTest() {
		$this->Addresses->Session->destroy();
		unset($this->Addresses);
		ClassRegistry::flush();
	}

	public function testToggleActivity() {
		$this->testAction('/user_addresses/toggle_activity/0/Address:3');
		$this->assertEqual($this->Addresses->Session->read('Message.flash.element'), 'flash'.DS.'success');

		$this->testAction('/user_addresses/toggle_activity/0/Address:1');
		$this->assertEqual($this->Addresses->Session->read('Message.flash.element'), 'flash'.DS.'success');

		$this->testAction('/user_addresses/toggle_activity/0/Address:2');
		$this->assertEqual($this->Addresses->Session->read('Message.flash.element'), 'flash'.DS.'failure');

		$this->testAction('/user_addresses/toggle_activity/1/Address:1');
		$this->assertEqual($this->Addresses->Session->read('Message.flash.element'), 'flash'.DS.'success');
	}

	public function testPrimary() {
		$this->testAction('/user_addresses/primary/Address:1');
		$address = $this->Addresses->Address->read(null, 1);
		$this->assertEqual($address['Address']['primary'], 1);
		$address = $this->Addresses->Address->read(null, 2);
		$this->assertEqual($address['Address']['primary'], 0);
		$address = $this->Addresses->Address->read(null, 3);
		$this->assertEqual($address['Address']['primary'], 1);

		$this->testAction('/user_addresses/primary/Address:2');
		$address = $this->Addresses->Address->read(null, 1);
		$this->assertEqual($address['Address']['primary'], 0);
		$address = $this->Addresses->Address->read(null, 2);
		$this->assertEqual($address['Address']['primary'], 1);
		$address = $this->Addresses->Address->read(null, 3);
		$this->assertEqual($address['Address']['primary'], 1);
	}

	public function testIndex() {
		$vars = $this->testAction('/user_addresses/index/User:1');
		$results = Set::extract('/Address/id', $vars['addresses']);
		sort($results);
		$this->assertEqual($results, array(1, 2));

		$vars = $this->testAction('/user_addresses/index/User:4');
		$result = $vars['addresses'];
		$this->assertEqual($result, array());
	}

	public function testAdd() {
		$data = array(
			'Address' => array(
				'name' => 'Work 2',
				'address_line_1' => '3080 Airway',
				'address_line_2' => '',
				'city' => 'Costa Mesa',
				'state' => 'CA',
				'zip' => 92886,
				'model' => 'User',
				'foreign_key' => 1,
				'primary' => 1
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

	public function testEdit() {
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

	public function testDelete() {
		$vars = $this->testAction('/user_addresses/delete/1');
		$this->assertFalse($this->Addresses->Address->read(null, 1));
	}

}
