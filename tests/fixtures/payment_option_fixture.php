<?php
/* PaymentOption Fixture generated on: 2010-06-28 09:06:31 : 1277741371 */
class PaymentOptionFixture extends CakeTestFixture {
	public $name = 'PaymentOption';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'involvement_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'total' => array('type' => 'decimal', 'null' => true, 'default' => NULL, 'length' => '10,2'),
		'deposit' => array('type' => 'decimal', 'null' => true, 'default' => NULL, 'length' => '10,2'),
		'childcare' => array('type' => 'decimal', 'null' => true, 'default' => NULL, 'length' => '10,2'),
		'account_code' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'tax_deductible' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'involvement_key' => array('column' => 'involvement_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
		array(
			'id' => 1,
			'involvement_id' => 1,
			'name' => 'Single Person',
			'total' => 25,
			'deposit' => NULL,
			'childcare' => NULL,
			'account_code' => '123',
			'tax_deductible' => 0,
			'created' => '2010-04-08 13:35:34',
			'modified' => '2010-04-08 13:35:34'
		),
		array(
			'id' => 2,
			'involvement_id' => 1,
			'name' => 'Single Person with Childcare',
			'total' => 25,
			'deposit' => NULL,
			'childcare' => 10,
			'account_code' => '123',
			'tax_deductible' => 0,
			'created' => '2010-04-08 13:41:16',
			'modified' => '2010-04-09 10:20:25'
		),
		array(
			'id' => 3,
			'involvement_id' => 3,
			'name' => 'Team CORE signups',
			'total' => 5,
			'deposit' => 2.50,
			'childcare' => NULL,
			'account_code' => '456',
			'tax_deductible' => 1,
			'created' => '2010-04-08 13:41:16',
			'modified' => '2010-04-09 10:20:25'
		),
		array(
			'id' => 4,
			'involvement_id' => 5,
			'name' => 'Test option',
			'total' => 100,
			'deposit' => 25,
			'childcare' => NULL,
			'account_code' => '456',
			'tax_deductible' => 1,
			'created' => '2010-04-08 13:41:16',
			'modified' => '2010-04-09 10:20:25'
		)
	);

	public function create(&$db) {
		$db->columns['decimal'] = array(
			'name' => 'decimal',
			'formatter' => 'floatval'
		);
		return parent::create($db);
	}
}
