<?php
/* Roster Test cases generated on: 2010-07-26 14:07:11 : 1280180951 */
App::import('Model', array('Roster', 'CreditCard'));

Mock::generatePartial('CreditCard', 'MockCreditCard', array('save'));

class RosterTestCase extends CakeTestCase {
	var $fixtures = array('app.roster', 'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.leader', 'app.role', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member', 'app.household', 'app.payment', 'app.payment_type', 'app.publication', 'app.publications_user', 'app.answer', 'app.log', 'app.ministries_rev', 'app.involvements_rev');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		$this->loadFixtures('Roster', 'Payment', 'PaymentOption', 'Involvement', 'PaymentType');
		$CreditCard = new MockCreditCard();
		$CreditCard->setReturnValue('save', true);
		ClassRegistry::removeObject('CreditCard');
		ClassRegistry::addObject('CreditCard', $CreditCard);
		$this->Roster =& ClassRegistry::init('Roster');
	}

	function endTest() {
		unset($this->Roster);		
		ClassRegistry::flush();
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
			'roster_status' => 1,
			'parent' => 1,
			'payment_option_id' => 2,
			'role_id' => null
		);
		$this->assertEqual($result, $expected);
	}

	function testSetDefaultDataNoPayment() {
		$involvement = $this->Roster->Involvement->read(null, 5);
		$defaults = array(
			'role_id' => 2
		);
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
				'user_id' => 1,
				'involvement_id' => 5,
				'roster_status' => 1,
				'parent' => null,
				'payment_option_id' => null,
				'role_id' => 2
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