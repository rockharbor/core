<?php
App::import('Lib', 'CoreTestCase');
App::import('Model', 'User');

class VirtualFieldModel extends AppModel {

	var $useTable = false;

	var $name = 'VirtualField';

	var $virtualFields = array(
		'name' => 'CONCAT(:ALIAS:.first_name, " ", :ALIAS:.last_name)',
	);

}

class AppModelTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('User', 'Group', 'Profile');
		$this->User =& ClassRegistry::init('User');
	}

	function endTest() {
		unset($this->User);
		ClassRegistry::flush();
	}

	function testAliasInVirtualFields() {
		$VirtualField = new VirtualFieldModel();
		$result = $VirtualField->getVirtualField('name');
		$expected = 'CONCAT(VirtualField.first_name, " ", VirtualField.last_name)';
		$this->assertEqual($result, $expected);

		$VirtualField = new VirtualFieldModel(array('alias' => 'SomeOtherName'));
		$result = $VirtualField->getVirtualField('name');
		$expected = 'CONCAT(SomeOtherName.first_name, " ", SomeOtherName.last_name)';
		$this->assertEqual($result, $expected);
	}

	function testPostContains() {
		$data = array(
			'User' => array(
				'username' => 'jharris'
			)
		);
		$results = $this->User->postContains($data);
		$expected = array();
		$this->assertEqual($results, $expected);

		$data = array(
			'User' => array(
				'username' => 'jharris'
			),
			'Profile' => array(
				'name' => 'Jeremy'
			)
		);
		$expected = array('Profile' => array());
		$results = $this->User->postContains($data);
		$this->assertEqual($results, $expected);

		$data = array(
			'User' => array(
				'username' => 'jharris'
			),
			'Profile' => array(
				'name' => 'Jeremy'
			),
			'NonExistantModel' => array(
				'field' => 'value'
			)
		);
		$expected = array('Profile' => array());
		$results = $this->User->postContains($data);
		$this->assertEqual($results, $expected);

		$data = array(
			'User' => array(
				'username' => 'jharris'
			),
			'Profile' => array(
				'name' => 'Jeremy'
			),
			'Household' => array(
				'field' => 'value'
			)
		);
		$expected = array('Profile' => array());
		$results = $this->User->postContains($data);
		$this->assertEqual($results, $expected);

		$data = array(
			'User' => array(
				'username' => 'jharris'
			),
			'Profile' => array(
				'name' => 'Jeremy'
			),
			'Publication' => array(
				'Publication' => array(
					0 => true
				)
			)
		);
		$expected = array(
			'Profile' => array(),
			'Publication' => array()
		);
		$results = $this->User->postContains($data);
		$this->assertEqual($results, $expected);
	}

	function testOwnedBy() {
		$this->loadFixtures('Address', 'Roster');
		$this->assertFalse($this->User->ownedBy());
		$this->assertTrue($this->User->ownedBy(1, 1));
		$this->assertTrue($this->User->Address->ownedBy(1, 1));
		$this->assertTrue($this->User->Profile->ownedBy(1, 1));
		$this->assertFalse($this->User->Profile->ownedBy(1, 2));
		$this->User->Roster->id = 2;
		$this->assertFalse($this->User->Roster->ownedBy(1));
		$this->assertTrue($this->User->Roster->ownedBy(2));
	}

	function testToggleActivity() {
		$this->assertFalse($this->User->toggleActivity());
		$this->assertTrue($this->User->toggleActivity(1));
		$this->assertEqual($this->User->field('active'), 0);
		$this->assertTrue($this->User->toggleActivity(1, true));
		$this->assertEqual($this->User->field('active'), 1);
		$this->assertTrue($this->User->toggleActivity(1, false, true));
		$this->assertEqual($this->User->field('active'), 0);

		$this->loadFixtures('Ministry');
		$this->Ministry =& ClassRegistry::init('Ministry');
		$this->Ministry->Behaviors->disable('Confirm');
		$this->Ministry->Involvement->Behaviors->disable('Confirm');
		$this->assertTrue($this->Ministry->toggleActivity(4, false, true));
		$this->assertEqual($this->Ministry->field('active'), 0);
		$this->Ministry->Involvement->id = 1;
		$this->assertEqual($this->Ministry->Involvement->field('active'), 0);
		$this->Ministry->Involvement->id = 3;
		$this->assertEqual($this->Ministry->Involvement->field('active'), 0);
	}

	function test_createPartialDates() {
		$data = array(
			'month' => 4,
			'day' => 14,
			'year' => 1984
		);		
		$this->assertEqual($this->User->Profile->_createPartialDates('birth_date', $data), $data);

		$data = array(
			'month' => 4,
			'day' => 14,
			'year' => ''
		);
		$expected = '0000-4-14';
		$results = $this->User->Profile->_createPartialDates('background_check_date', $data);
		$this->assertEqual($results, $expected);

		$data = array(
			'month' => 4,
			'day' => '',
			'year' => 1984
		);
		$expected = '1984-4-00';
		$results = $this->User->Profile->_createPartialDates('background_check_date', $data);
		$this->assertEqual($results, $expected);
	}
}
?>