<?php

App::import('Vendor', 'Install.Records');

class RequestStatusRecords extends Records {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'RequestStatus';

/**
 * Record to import upon install
 *
 * @var array
 */
	var $records = array(
		array(
			'id' => 1,
			'name' => 'Confirmed',
		),
		array(
			'id' => 2,
			'name' => 'Pending',
		),
		array(
			'id' => 3,
			'name' => 'Denied',
		),
		array(
			'id' => 4,
			'name' => 'Completed',
		)
	);


}

?>
