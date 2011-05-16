<?php

class PaymentTypeRecords extends Records {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'PaymentType';

/**
 * Record to import upon install
 *
 * @var array
 */
	var $records = array(
		array(
			'id' => 1,
			'name' => 'Visa',
			'type' => 0,
			'group_id' => 8
		),
		array(
			'id' => 2,
			'name' => 'Mastercard',
			'type' => 0,
			'group_id' => 8
		),
		array(
			'id' => 3,
			'name' => 'American Express',
			'type' => 0,
			'group_id' => 8
		),
		array(
			'id' => 4,
			'name' => 'Cash',
			'type' => 1,
			'group_id' => 5
		),
		array(
			'id' => 5,
			'name' => 'Check',
			'type' => 2,
			'group_id' => 5
		),
	);


}

?>
