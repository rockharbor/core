<?php
/* PaymentType Fixture generated on: 2010-06-28 09:06:38 : 1277741378 */
class PaymentTypeFixture extends CakeTestFixture {
	var $name = 'PaymentType';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'type' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 2),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'Visa',
			'type' => 0,
			'created' => '2010-03-17 09:07:40',
			'modified' => '2010-03-17 09:07:40'
		),
		array(
			'id' => 2,
			'name' => 'Cash',
			'type' => 1,
			'created' => '2010-03-17 09:07:47',
			'modified' => '2010-03-17 09:07:47'
		),
		array(
			'id' => 3,
			'name' => 'Mastercard',
			'type' => 0,
			'created' => '2010-03-17 09:07:40',
			'modified' => '2010-03-17 09:07:40'
		),
		array(
			'id' => 4,
			'name' => 'Check',
			'type' => 2,
			'created' => '2010-03-17 09:07:53',
			'modified' => '2010-03-17 09:07:53'
		),
		array(
			'id' => 5,
			'name' => 'Scholarship',
			'type' => 1,
			'created' => '2010-03-17 09:08:00',
			'modified' => '2010-03-17 09:08:00'
		),
	);
}
?>