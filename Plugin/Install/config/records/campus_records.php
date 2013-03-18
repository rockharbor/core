<?php

class CampusRecords extends Records {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Campus';

/**
 * Record to import upon install
 *
 * @var array
 */
	var $records = array(
		array(
			'id' => 1,
			'name' => 'Main Campus',
			'description' => 'The main campus.',
			'active' => true
		)
	);


}

