<?php

class ClassificationRecords extends Records {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Classification';

/**
 * Record to import upon install
 *
 * @var array
 */
	var $records = array(
		array(
			'id' => 1,
			'name' => 'Weekend Attender At Fullerton'
		),
		array(
			'id' => 2,
			'name' => 'Weekend Attender At South County'
		),
		array(
			'id' => 3,
			'name' => 'Weekend Attender At Central'
		),
		array(
			'id' => 4,
			'name' => 'I Listen To The Podcasts'
		),
		array(
			'id' => 5,
			'name' => 'I\'m Curious About ROCKHARBOR'
		),
		array(
			'id' => 6,
			'name' => 'I\'m A Friend But Don\'t Attend'
		)
	);

}

?>
