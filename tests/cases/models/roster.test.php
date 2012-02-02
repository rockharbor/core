<?php
/* Roster Test cases generated on: 2010-07-26 14:07:11 : 1280180951 */
App::import('Lib', 'CoreTestCase');
App::import('Model', array('Roster', 'CreditCard'));

Mock::generatePartial('CreditCard', 'MockRosterCreditCard', array('save'));

class RosterTestCase extends CoreTestCase {
	function startTest() {
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
				'user_id' => 2
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
			'user_id' => 2,
			'amount' => 10,
			'payment_type_id' => 1,
			'number' => 1234,
			'payment_placed_by' => 50,
			'payment_option_id' => 2
		);
		$this->assertEqual($result, $expected);
		
		$result = $newRoster['Roster'];		
		$expected = array(
			'user_id' => 2,
			'involvement_id' => 1,
			'roster_status_id' => 4,
			'parent' => 1,
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
				'parent' => null,
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
			'defaults' => array(
				'pay_later' => true
			),
			'involvement' => $this->Roster->Involvement->read(null, 1)
		));
		$expected = array(
			'Roster' => array(
				'involvement_id' => 1,
				'roster_status_id' => 4,
				'parent' => null,
				'payment_option_id' => 1
			)
		);
		$this->assertEqual($result, $expected);
		$this->assertTrue($this->Roster->save($expected));
	}

	function testVirtualFields() {
		$roster = $this->Roster->read(null, 6);

		$result = $roster['Roster']['amount_paid'];
		$this->assertEqual($result, 20);

		$result = $roster['Roster']['amount_due'];
		$this->assertEqual($result, 100);

		$result = $roster['Roster']['balance'];
		$this->assertEqual($result, 80);
	}

}
?>