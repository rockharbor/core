<?php
/* Households Test cases generated on: 2010-07-12 10:07:39 : 1278957279 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail.QueueEmail'));
App::import('Controller', 'Households');

Mock::generatePartial('QueueEmailComponent', 'MockHouseholdsQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('HouseholdsController', 'TestHouseholdsController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class HouseholdsControllerTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Household', 'HouseholdMember', 'User', 'Profile', 'Group');
		$this->Households =& new TestHouseholdsController();
		$this->Households->__construct();
		$this->Households->constructClasses();
		$this->Households->Notifier->QueueEmail = new MockHouseholdsQueueEmailComponent();
		$this->Households->Notifier->QueueEmail->enabled = true;
		$this->Households->Notifier->QueueEmail->initialize($this->Households);
		$this->Households->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Households->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->Households->setReturnValue('isAuthorized', true);
		$this->testController = $this->Households;
	}

	function endTest() {
		$this->Households->Session->destroy();
		unset($this->Households);		
		ClassRegistry::flush();
	}

	function testConfirm() {
		$this->testAction('/households/confirm/1/3/User:1');
		$results = $this->Households->Household->HouseholdMember->read();
		$this->assertTrue($results['HouseholdMember']['confirmed']);
	}

	function testInvite() {
		$this->assertTrue($this->Households->Household->isMember(1, 1));
		$this->testAction('/households/invite/1/1/User:1');
		$this->assertTrue($this->Households->Household->isMember(1, 1));
		
		$this->testAction('/households/invite/5/2/User:1');
		$this->assertTrue($this->Households->Household->isMember(5, 2));
		
		$invites = $this->Households->Household->HouseholdMember->User->Invitation->getInvitations(5);
		$invitation = $this->Households->Household->HouseholdMember->User->Invitation->read(null, $invites[0]);
		$results = $invitation['Invitation']['confirm_action'];
		$expected = '/households/confirm/5/2';
		$this->assertEqual($results, $expected);
		$results = $invitation['Invitation']['deny_action'];
		$expected = '/households/delete/5/2';
		$this->assertEqual($results, $expected);
	}
	
	function testDelete() {
		$this->testAction('/households/delete/3/1');
		$this->assertFalse($this->Households->Household->isMember(3, 1));
		
		$this->testAction('/households/delete/3/2');
		$this->assertFalse($this->Households->Household->isMember(3, 2));
		
		// deleting from last household will not work
		$this->testAction('/households/delete/3/3');
		$this->assertTrue($this->Households->Household->isMember(3, 3));
		
		// only remove if they are confirmed somewhere else
		$this->testAction('/households/delete/97/5');
		// they were removed but a new confirmed household was created
		$this->assertNotNull($this->Households->Household->id);
		$this->assertTrue($this->Households->Household->isMember(97, $this->Households->Household->id));
	}

	function testMakeHouseholdContact() {
		// have to make them a member first
		$this->Households->Household->HouseholdMember->save(array(
			'user_id' => 1,
			'household_id' => 2,
			'confirmed' => true
		));
		$this->testAction('/households/make_household_contact/1/2/User:1');
		$results = $this->Households->Household->find('all', array(
			'fields' => array('id', 'contact_id')
		));
		$expected = array(
			array(
				'Household' => array(
					'id' => 1,
					'contact_id' => 1
				)
			),
			array(
				'Household' => array(
					'id' => 2,
					'contact_id' => 1
				)
			),
			array(
				'Household' => array(
					'id' => 3,
					'contact_id' => 3
				)
			),
			array(
				'Household' => array(
					'id' => 6,
					'contact_id' => 1
				)
			)
		);
		$this->assertEqual($results, $expected);
	}

	function testIndex() {
		$vars = $this->testAction('/households/index/User:1', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Household/id', $vars['households']);
		$expected = array(1, 6);
		$this->assertEqual($results, $expected);

		$this->Households->Household->HouseholdMember->save(array(
			'user_id' => 1,
			'household_id' => 2,
			'confirmed' => true
		));
		$vars = $this->testAction('/households/index/User:1', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Household/id', $vars['households']);
		$expected = array(1, 2, 6);
		$this->assertEqual($results, $expected);
	}

}
