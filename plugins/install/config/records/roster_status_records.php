<?php

class RosterStatusRecords extends Records {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'RosterStatus';

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
			'name' => 'Invited',
		),
		array(
			'id' => 4,
			'name' => 'Declined',
		)
	);


}

?>
