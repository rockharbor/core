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
			'name' => 'Weekend Attender'
		),
		array(
			'id' => 2,
			'name' => 'I Listen To The Podcasts'
		),
		array(
			'id' => 3,
			'name' => 'I\'m Curious'
		),
		array(
			'id' => 4,
			'name' => 'I\'m A Friend But Don\'t Attend'
		),
		array(
			'id' => 5,
			'name' => 'I\'m A Visitor'
		)
	);

}

