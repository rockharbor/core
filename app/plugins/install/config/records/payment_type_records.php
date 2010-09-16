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
			'name' => 'Credit Card',
		),
		array(
			'id' => 2,
			'name' => 'Cash',
		),
		array(
			'id' => 3,
			'name' => 'Check',
		),
	);


}

?>
