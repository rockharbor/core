<?php
App::import('Lib', 'CoreTestCase');
App::import('Model', 'User');

class UserProxy extends User {
	public $name = 'User';
	public $alias = 'User';
	function afterFind($results, $primary) {
		return $results;
	}
}

class VirtualFieldModel extends AppModel {

	var $useTable = false;

	var $name = 'VirtualField';

	var $virtualFields = array(
		'name' => 'CONCAT(:ALIAS:.first_name, " ", :ALIAS:.last_name)',
	);

	var $_schema = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'first_name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 32),
		'last_name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 32),
		'flagged' => array('type' => 'boolean', 'null' => false, 'default' => NULL),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
	);

}

class AppModelTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('User', 'Group', 'Profile');
		$this->User =& ClassRegistry::init('UserProxy');
	}

	function endTest() {
		unset($this->User);
		ClassRegistry::flush();
	}

	function testScopeConditions() {
		$data = array(
			'active' => false,
			'private' => true
		);
		$result = $this->User->scopeConditions($data);
		$expected = array(
			'User.active' => false
		);
		$this->assertEqual($result, $expected);

		$data = array(
			'active' => false,
			'private' => true,
			'Ministry.private' => false
		);
		$result = $this->User->scopeConditions($data);
		$expected = array(
			'User.active' => false,
			'Ministry.private' => false
		);
		$this->assertEqual($result, $expected);

		$data = array(
			'private' => 'no',
			'first_name' => 'jeremy',
			'name' => 'jeremy harris'
		);
		$result = ClassRegistry::init('VirtualFieldModel')->scopeConditions($data);
		$cls = new stdClass();
		$cls->type = 'expression';
		$cls->value = 'CONCAT(VirtualFieldModel.first_name, " ", VirtualFieldModel.last_name)';
		$expected = array(
			'VirtualFieldModel.first_name' => 'jeremy',
			'VirtualFieldModel.name' => 'jeremy harris'
		);
		$this->assertEqual($result, $expected);
	}

	function testDefaultImage() {
		$this->loadFixtures('Attachment');
		$this->loadSettings();
		$this->User->Image->Behaviors->detach('Media.Coupler');
		
		$find = $this->User->read(null, 1);
		$this->assertFalse(isset($find['Image']));
		$this->assertFalse(isset($find['ImageIcon']));

		$this->User->contain(array('Image'));
		$find = $this->User->read(null, 1);
		$this->assertEqual($find['Image'][0]['id'], 4);
		$results = $this->User->defaultImage($find);
		$this->assertEqual($results['Image'][0]['alternative'], 'Profile photo');
		$this->assertEqual($results['ImageIcon']['alternative'], 'Profile photo');

		$this->User->contain(array('Image'));
		$find = $this->User->read(null, 2);
		$this->assertEqual($find['Image'], array());
		$results = $this->User->defaultImage($find);
		$this->assertEqual($results['Image'][0]['alternative'], 'Default profile photo');
		$this->assertEqual($results['ImageIcon']['alternative'], 'Default profile photo');

		// add an icon image and make sure it's used instead of the default image
		// if the user doesn't have an image
		$icon = $this->User->Image->read(null, 5);
		unset($icon['Image']['id']);
		$icon['Image']['alternative'] = 'Default icon photo';
		$icon['Image']['foreign_key'] = 25;
		$this->User->Image->create();
		$this->assertTrue($this->User->Image->save($icon, array('callbacks' => false)));
		$this->loadSettings();

		$this->User->contain(array('Image'));
		$find = $this->User->read(null, 1);
		$this->assertEqual($find['Image'][0]['id'], 4);
		$results = $this->User->defaultImage($find);
		$this->assertEqual($results['Image'][0]['id'], 4);
		$this->assertEqual($results['ImageIcon']['id'], 4);

		$this->User->contain(array('Image'));
		$find = $this->User->read(null, 2);
		$this->assertEqual($find['Image'], array());
		$results = $this->User->defaultImage($find);
		$this->assertEqual($results['Image'][0]['alternative'], 'Default profile photo');
		$this->assertEqual($results['ImageIcon']['alternative'], 'Default icon photo');

		$this->unloadSettings();
	}

	function testMakeFulltext() {
		$data = array(
			'simple' => 'soft* cuddly*'
		);
		$result = $this->User->Profile->makeFulltext($data);
		$result = $result[0]->value;
		$expected = 'MATCH (`Profile`.`first_name`,`Profile`.`last_name`) AGAINST (\'soft* cuddly*\' IN BOOLEAN MODE)';
		$this->assertEqual($result, $expected);
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
		$this->loadFixtures('Campus');
		
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
		
		$data = array(
			 'Profile' => array(
				  'Campus' => array(
						'name' => 'Some campus'
				  )
			 )
		);
		$expected = array(
			 'Profile' => array(
				  'Campus' => array()
			 )
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

	function testDeconstruct() {
		$data = array(
			'month' => 4,
			'day' => 14,
			'year' => 1984
		);
		$results = $this->User->Profile->deconstruct('birth_date', $data);
		$expected = '1984-4-14';
		$this->assertEqual($results, $expected);

		$data = array(
			'month' => 4,
			'day' => 14,
			'year' => ''
		);
		$expected = '0000-4-14';
		$results = $this->User->Profile->deconstruct('background_check_date', $data);
		$this->assertEqual($results, $expected);

		$data = array(
			'month' => 4,
			'day' => '',
			'year' => 1984
		);
		$expected = '1984-4-00';
		$results = $this->User->Profile->deconstruct('background_check_date', $data);
		$this->assertEqual($results, $expected);
	}
}
?>