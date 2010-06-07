<?php
class Address extends AppModel {
	var $name = 'Address';
	
	var $actsAs = array(
		'GeoCoordinate'
	);
	
	var $belongsTo = array(
		'Zipcode' => array(
			'foreignKey' => false,
			'conditions' => array('Zipcode.zip = Address.zip')
		)
	);
	
	var $validate = array(
		'name' => array(
			'rule' => array('custom', '/^[a-z0-9 ]*$/i'),
			'required' => false,
			'allowEmpty' => false,
			'message' => 'Alpha-numeric characters only.'
		),
		'address_line_1' => array('rule' => 'notEmpty'),
		'city' => array('rule' => 'notEmpty'),
		'state' => array('rule' => 'notEmpty'),
		'zip' => array(
			'rule' => array('postal', null, 'us'),
			'message' => 'Please enter a valid zipcode.',
			'allowEmpty' => false
		)
	);

/**
 * Returns a Cake virtual field SQL string for the distance 
 * from a latitude and longitude
 *
 * @param float $lat Latitude
 * @param float $lng Longitude
 * @return string SQL string
 * @access public
 */ 
	function distance($lat, $lng) {
		return "(
			(ACOS(SIN($lat * PI() / 180) 
			* SIN(Address.lat * PI() / 180) 
			+ COS($lat * PI() / 180) 
			* COS(Address.lat * PI() / 180) 
			* COS(($lng - Address.lng) * PI() / 180)) 
			* 180 / PI()) * 60 * 1.1515
		)"; 
	}	
}
?>