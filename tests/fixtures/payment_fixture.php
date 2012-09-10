<?php
/* Payment Fixture generated on: 2010-06-28 09:06:44 : 1277741384 */
class PaymentFixture extends CakeTestFixture {
	var $name = 'Payment';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'roster_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'amount' => array('type' => 'decimal', 'null' => true, 'default' => NULL, 'length' => '10,2'),
		'payment_type_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'number' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'transaction_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'payment_placed_by' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'refunded' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'comment' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 2500),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'belongsto_key' => array('column' => array('user_id', 'roster_id', 'payment_type_id', 'payment_placed_by'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'user_id' => 1,
			'roster_id' => 4,
			'amount' => 25,
			'payment_type_id' => 1,
			'number' => '0027',
			'transaction_id' => '1234',
			'payment_placed_by' => 1,
			'refunded' => 0,
			'created' => '2010-05-04 07:33:03',
			'modified' => '2010-05-04 07:33:03',
			'comment' => 'Jeremy Harris\'s card processed by Jeremy Harris.'
		),
		array(
			'id' => 2,
			'user_id' => 2,
			'roster_id' => 4,
			'amount' => 2.50,
			'payment_type_id' => 1,
			'number' => '0027',
			'transaction_id' => '1234',
			'payment_placed_by' => 1,
			'refunded' => 0,
			'created' => '2010-05-04 07:33:03',
			'modified' => '2010-05-04 07:33:03',
			'comment' => 'Jeremy Harris\'s card processed by Jeremy Harris.'
		),
		array(
			'id' => 3,
			'user_id' => 2,
			'roster_id' => 4,
			'amount' => 2.50,
			'payment_type_id' => 2,
			'number' => NULL,
			'transaction_id' => NULL,
			'payment_placed_by' => 2,
			'refunded' => 0,
			'created' => '2010-05-06 07:33:03',
			'modified' => '2010-05-06 07:33:03',
			'comment' => 'Ricky made a cash payment to pay his balance.'
		),
		array(
			'id' => 6,
			'user_id' => 5,
			'roster_id' => 6,
			'amount' => 20,
			'payment_type_id' => 2,
			'number' => NULL,
			'transaction_id' => NULL,
			'payment_placed_by' => 5,
			'refunded' => 0,
			'created' => '2010-05-08 07:33:03',
			'modified' => '2010-05-08 07:33:03',
			'comment' => 'Invisible user made a payment'
		),
	);
	
	function create(&$db) {
		$db->columns['decimal'] = array(
			'name' => 'decimal',
			'formatter' => 'floatval'
		);
		return parent::create($db);
	}
}
