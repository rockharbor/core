<?php
class Zipcode extends AppModel {
	var $name = 'Zipcode';
	
	var $validate = array(
		'zip' => array(
			'rule' => array('postal', null, 'us'),
			'message' => 'Please enter a valid zipcode.',
			'allowEmpty' => false
		)
	);

	var $belongsTo = array(
		'Region'
	);

}
