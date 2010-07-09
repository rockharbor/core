<?php
/* Addresses Test cases generated on: 2010-07-02 11:07:49 : 1278096229 */
App::import('Controller', 'UserAddresses');

class TestAddressesController extends UserAddressesController {
	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
	
	function _stop($status = 0) {
		$this->stopped = $status;
	}
}

class AddressesControllerTestCase extends CakeTestCase {

	var $fixtures = array('app.address');

	function startTest() {
		$this->Addresses = new TestAddressesController();
		$this->Addresses->constructClasses();		
		$this->Addresses->Component->initialize($this->Addresses);
		$this->Addresses->Session->write('Auth.User', array('id' => 1));
		$this->Addresses->Session->write('User', array('Group' => array('id' => 1)));
	}

	function endTest() {
		$this->Addresses->Session->destroy();
		unset($this->Addresses);
		ClassRegistry::flush();
	}

	function testIndex() {
		$vars = $this->testAction('/test_addresses/index/User:1', array(
			'return' => 'vars'
		));
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

		$vars = $this->testAction('/test_addresses/index/User:2', array(
			'return' => 'vars'
		));
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
		$vars = $this->testAction('/test_addresses/add', array(
			'return' => 'vars',
			'data' => $data
		));
		$this->Addresses->Address->id = 3;
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
		$vars = $this->testAction('/test_addresses/edit/1', array(
			'return' => 'vars',
			'data' => $data
		));
		$this->Addresses->Address->id = 1;
		$this->assertEqual($this->Addresses->Address->field('primary'), 0);
		$this->Addresses->Address->id = 2;
		$this->assertEqual($this->Addresses->Address->field('primary'), 1);

		$data['Address']['primary'] = 1;
		$vars = $this->testAction('/test_addresses/edit/1', array(
			'return' => 'vars',
			'data' => $data
		));
		$this->Addresses->Address->id = 1;
		$this->assertEqual($this->Addresses->Address->field('primary'), 1);
		$this->Addresses->Address->id = 2;
		$this->assertEqual($this->Addresses->Address->field('primary'), 0);
	}

	function testDelete() {
		$vars = $this->testAction('/test_addresses/delete/1');
		$this->assertFalse($this->Addresses->Address->read(null, 1));
	}

}
?>