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
		)
	);

}

?>
