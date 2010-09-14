<?php

class InvolvementTypeRecords extends Records {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'InvolvementType';

/**
 * Record to import upon install
 *
 * @var array
 */
	var $records = array(
		array(
			'id' => 1,
			'name' => 'Event',
		),
		array(
			'id' => 2,
			'name' => 'Team',
		)
	);


}

?>
