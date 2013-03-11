<?php
class Zipcode extends AppModel {
	public $name = 'Zipcode';

	public $validate = array(
		'zip' => array(
			'rule' => array('postal', null, 'us'),
			'message' => 'Please enter a valid zipcode.',
			'allowEmpty' => false
		)
	);

	public $belongsTo = array(
		'Region'
	);

}
