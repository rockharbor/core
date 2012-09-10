<?php
/* Roster Test cases generated on: 2010-07-26 14:07:11 : 1280180951 */
App::import('Lib', 'CoreTestCase');
App::import('Model', array('Roster', 'CreditCard'));

Mock::generatePartial('CreditCard', 'MockRosterCreditCard', array('save'));

class RosterTestCase extends CoreTestCase {
	function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Roster', 'Payment', 'PaymentOption', 'Involvement', 'PaymentType', 'Role', 'RolesRoster', 'RosterStatus');
		$CreditCard = new MockRosterCreditCard();
		$CreditCard->setReturnValue('save', true);
		ClassRegistry::removeObject('CreditCard');
		ClassRegistry::addObject('CreditCard', $CreditCard);
		$this->Roster =& ClassRegistry::init('Roster');
	}

	function endTest() {
		unset($this->Roster);		
		ClassRegistry::flush();
	}
	
	function testFindByRoles() {
		$data = array(
			'roles' => array(1)
		);
		$results = $this->Roster->findByRoles($data);
		$expected = "SELECT `RolesRoster`.`roster_id` 
			FROM `roles_rosters` AS `RolesRoster` 
			LEFT JOIN `roles` AS `Role` ON (`RolesRoster`.`role_id` = `Role`.`id`) 
			WHERE EXISTS 
			(SELECT 1 FROM roles_rosters WHERE role_id = 1 AND roster_id = `RolesRoster`.`roster_id`) ";
		$this->assertEqual($this->singleLine($results), $this->singleLine($expected));
		
		$data = array(
			'roles' => array(1, 2)
		);
		$results = $this->Roster->findByRoles($data);
		$expected = "SELECT `RolesRoster`.`roster_id` 
			FROM `roles_rosters` AS `RolesRoster` 
			LEFT JOIN `roles` AS `Role` ON (`RolesRoster`.`role_id` = `Role`.`id`) 
			WHERE 
			EXISTS (SELECT 1 FROM roles_rosters WHERE role_id = 1 AND roster_id = `RolesRoster`.`roster_id`) 
			AND 
			EXISTS (SELECT 1 FROM roles_rosters WHERE role_id = 2 AND roster_id = `RolesRoster`.`roster_id`) ";
		$this->assertEqual($this->singleLine($results), $this->singleLine($expected));
	}
	
	function testPaginateCount() {
		$conditions = array(
			'Roster.involvement_id' => 1
		);
		$recursive = 1;
		$extra = array(
			'contain' => array(
				'Involvement'
			)
		);
		$results = $this->Roster->paginateCount($conditions, $recursive, $extra);
		$this->assertEqual($results, 2);
		
		$conditions = array(
			'Involvement.id' => 1
		);
		$recursive = 1;
		$extra = array(
			'link' => array(
				'Involvement'
			)
		);
		$results = $this->Roster->paginateCount($conditions, $recursive, $extra);
		$this->assertEqual($results, 2);
	}

	function testSetDefaultAlreadySignedUp() {
		$involvement = $this->Roster->Involvement->read(null, 1);
		$defaults = array(
			'payment_option_id' => 1,
			'payment_type_id' => 1,
			'pay_later' => false,
			'pay_deposit_amount' => false,
		);
		$creditCard = array(
			'CreditCard' => array(
				'first_name' => 'Joe',
				'last_name' => 'Schmoe',
				'credit_card_number' => '1234567891001234'
			)
		);
		$payer = array(
			'User' => array(
				'id' => 50
			),
			'Profile' => array(
				'name' => 'Some guy'
			)
		);
		
		$results = $this->Roster->setDefaultData(array(
			'roster' => array(
				'Roster' => array(
					'user_id' => 2
				)
			),
			'defaults' => $defaults,
			'involvement' => $involvement,
			'creditCard' => $creditCard,
			'payer' => $payer
		));
		$this->assertEqual($results['Roster']['roster_status_id'], 1);
		
		$rosterCountBefore = $this->Roster->find('count');
		$paymentCountBefore = $this->Roster->Payment->find('count');
		$this->assertTrue($this->Roster->saveAll($results));
		$rosterCountAfter = $this->Roster->find('count');
		$paymentCountAfter = $this->Roster->Payment->find('count');
		
		// assert that they were confirmed and a payment was made
		$this->Roster->contain(array('Payment'));
		$results = $this->Roster->read(null, 2);
		$this->assertEqual($results['Roster']['roster_status_id'], 1);
		$this->assertEqual($rosterCountBefore, $rosterCountAfter);
		$this->assertEqual($paymentCountAfter-$paymentCountBefore, 1);
		$this->assertEqual($results['Payment'][0]['roster_id'], 2);
		
		$results = $this->Roster->setDefaultData(array(
			'roster' => array(
				'Roster' => array(
					'user_id' => 1
				)
			),
			'defaults' => $defaults,
			'involvement' => $involvement,
			'creditCard' => $creditCard,
			'payer' => $payer
		));
		$this->assertEqual($results['Roster']['roster_status_id'], 4);
	}

	function testRoles() {
		$this->Roster->contain(array('Role', 'RosterStatus'));
		$results = $this->Roster->read(null, 5);
		$results = Set::extract('/Role/id', $results);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);

		$roster = $this->Roster->read(null, 1);
		$roster['Role']['Role'] = array(2, 3);
		$this->Roster->saveAll($roster);
		$this->Roster->contain(array('Role'));
		$results = $this->Roster->read();
		$results = Set::extract('/Role/id', $results);
		$expected = array(2, 3);
		$this->assertEqual($results, $expected);
	}
	
	function testSetDefaultDataEmptyPayment() {
		$this->Roster->Involvement->PaymentOption->create();
		$this->Roster->Involvement->PaymentOption->save(array(
			'PaymentOption' => array(
				'involvement_id' => 3,
				'name' => 'Cheap as Free',
				'total' => 0,
				'deposit' => 0,
				'childcare' => null,
				'account_code' => '123',
				'tax_deductible' => 0
			)
		));
		$defaults = array(
			'payment_option_id' => $this->Roster->Involvement->PaymentOption->id,
			'payment_type_id' => 1,
			'pay_later' => false,
			'pay_deposit_amount' => false
		);
		$involvement = $this->Roster->Involvement->read(null, 3);
		$roster = array(
			'Roster' => array(
				'user_id' => 1
			)
		);

		$newRoster = $this->Roster->setDefaultData(compact(
			'roster', 'involvement', 'defaults'
		));

		$result = $newRoster;
		$expected = array(
			'Roster' => array(
				'id' => 5, // this user is already signed up, so it just gets modified
				'user_id' => 1,
				'involvement_id' => 3,
				'roster_status_id' => 1,
				'parent_id' => null,
				'payment_option_id' => 5,
			)
		);
		$this->assertEqual($result, $expected);
		
		$this->Roster->Involvement->PaymentOption->create();
		$this->Roster->Involvement->PaymentOption->save(array(
			'PaymentOption' => array(
				'involvement_id' => 3,
				'name' => 'Cheap as Free',
				'total' => 0,
				'deposit' => NULL,
				'childcare' => 10,
				'account_code' => '123',
				'tax_deductible' => 0
			)
		));
		$defaults = array(
			'payment_option_id' => $this->Roster->Involvement->PaymentOption->id,
			'payment_type_id' => 1,
			'pay_later' => false,
			'pay_deposit_amount' => false
		);
		$involvement = $this->Roster->Involvement->read(null, 3);
		$roster = array(
			'Roster' => array(
				'user_id' => 1
			)
		);

		$parent = 2;
		$creditCard = array(
			'CreditCard' => array(
				'first_name' => 'Joe',
				'last_name' => 'Schmoe',
				'credit_card_number' => '1234567891001234'
			)
		);
		$payer = array(
			'User' => array(
				'id' => 1
			),
			'Profile' => array(
				'name' => 'Some guy'
			)
		);
		$result = $this->Roster->setDefaultData(compact(
			'roster', 'involvement', 'defaults', 'parent', 'creditCard', 'payer'
		));
		$expected = array(
			'Roster' => array(
				'id' => 5, // this user is already signed up, so it just gets modified
				'user_id' => 1,
				'involvement_id' => 3,
				'roster_status_id' => 1,
				'parent_id' => 2,
				'payment_option_id' => 6,
			),
			'Payment' => array(
				0 => array(
					'user_id' => 1,
					'amount' => 10,
					'payment_type_id' => 1,
					'number' => 1234,
					'payment_placed_by' => 1,
					'payment_option_id' => 6
				)
			)
		);
		unset($result['Payment'][0]['comment']);
		$this->assertEqual($result, $expected);
	}
	
	function testSetDefaultChildcare() {
		$involvement = $this->Roster->Involvement->read(null, 1);
		$parent = 1;
		$defaults = array(
			'payment_option_id' => 2,
			'payment_type_id' => 1,
			'pay_later' => false,
			'pay_deposit_amount' => false
		);
		$roster = array(
			'Roster' => array(
				'user_id' => 12
			)
		);
		$creditCard = array(
			'CreditCard' => array(
				'first_name' => 'Joe',
				'last_name' => 'Schmoe',
				'credit_card_number' => '1234567891001234'
			)
		);
		$payer = array(
			'User' => array(
				'id' => 50
			),
			'Profile' => array(
				'name' => 'Some guy'
			)
		);

		$newRoster = $this->Roster->setDefaultData(compact(
			'roster', 'defaults', 'involvement', 'payer',
			'creditCard', 'parent'
		));
		unset($newRoster['Payment'][0]['comment']);

		$result = $newRoster['Payment'][0];
		$expected = array(
			'user_id' => 12,
			'amount' => 10,
			'payment_type_id' => 1,
			'number' => 1234,
			'payment_placed_by' => 50,
			'payment_option_id' => 2
		);
		$this->assertEqual($result, $expected);
		
		$result = $newRoster['Roster'];		
		$expected = array(
			'user_id' => 12,
			'involvement_id' => 1,
			'roster_status_id' => 4,
			'parent_id' => 1,
			'payment_option_id' => 2,
		);
		$this->assertEqual($result, $expected);
	}

	function testSetDefaultDataNoPayment() {
		$involvement = $this->Roster->Involvement->read(null, 5);
		$roster = array(
			'Roster' => array(
				'user_id' => 1
			)
		);

		$newRoster = $this->Roster->setDefaultData(compact(
			'roster', 'involvement'
		));

		$result = $newRoster;
		$expected = array(
			'Roster' => array(
				'user_id' => 1,
				'involvement_id' => 5,
				'roster_status_id' => 1,
				'parent_id' => null,
				'payment_option_id' => null,
			)
		);
		$this->assertEqual($result, $expected);
	}

	function testSetDefaultData() {
		$involvement = $this->Roster->Involvement->read(null, 1);
		$defaults = array(
			'payment_option_id' => 1,
			'payment_type_id' => 1,
			'pay_later' => false,
			'pay_deposit_amount' => false
		);
		$roster = array(
			'Roster' => array(
				'user_id' => 1
			)
		);
		$creditCard = array(
			'CreditCard' => array(
				'first_name' => 'Joe',
				'last_name' => 'Schmoe',
				'credit_card_number' => '1234567891001234'
			)
		);
		$payer = array(
			'User' => array(
				'id' => 50
			),
			'Profile' => array(
				'name' => 'Some guy'
			)
		);
		$newRoster = $this->Roster->setDefaultData(compact(
			'roster', 'defaults', 'involvement', 'payer',
			'creditCard'
		));
		unset($newRoster['Payment'][0]['comment']);

		$result = $newRoster['Payment'][0];
		$expected = array(
			'user_id' => 1,
			'amount' => 25,
			'payment_type_id' => 1,
			'number' => 1234,
			'payment_placed_by' => 50,
			'payment_option_id' => 1
		);
		$this->assertEqual($result, $expected);

		$involvement = $this->Roster->Involvement->read(null, 1);
		$defaults = array(
			'payment_option_id' => 1,
			'payment_type_id' => 1,
			'pay_later' => false,
			'pay_deposit_amount' => false
		);
		$roster = array(
			'Roster' => array(
				'user_id' => 1
			)
		);
		$creditCard = array(
			'CreditCard' => array(
				'first_name' => 'Joe',
				'last_name' => 'Schmoe',
				'credit_card_number' => '1234567891001234'
			)
		);
		$payer = array(
			'User' => array(
				'id' => 50
			),
			'Profile' => array(
				'name' => 'Some guy'
			)
		);

		$newRoster = $this->Roster->setDefaultData(compact(
			'roster', 'defaults', 'involvement', 'payer',
			'creditCard'
		));
		unset($newRoster['Payment'][0]['comment']);

		$result = $newRoster['Payment'][0];
		$expected = array(
			'user_id' => 1,
			'amount' => 25,
			'payment_type_id' => 1,
			'number' => 1234,
			'payment_placed_by' => 50,
			'payment_option_id' => 1
		);
		$this->assertEqual($result, $expected);
		
		$result = $this->Roster->setDefaultData(array(
			'roster' => $roster,
			'defaults' => array(
				'pay_later' => true
			),
			'involvement' => $this->Roster->Involvement->read(null, 1)
		));
		$expected = array(
			'Roster' => array(
				'user_id' => 1,
				'involvement_id' => 1,
				'roster_status_id' => 4,
				'parent_id' => null,
				'payment_option_id' => 1
			)
		);
		$this->assertEqual($result, $expected);
		$this->assertTrue($this->Roster->save($expected));
	}

	function testVirtualFields() {
		$roster = $this->Roster->read(null, 6);

		$result = $roster['Roster']['amount_paid'];
		$this->assertIdentical($result, '20.00');

		$result = $roster['Roster']['amount_due'];
		$this->assertIdentical($result, '100.00');

		$result = $roster['Roster']['balance'];
		$this->assertIdentical($result, '80.00');
	}
	
}
