<?php
/**
 * Address model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Address model
 *
 * Polymorphic model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Address extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Address';

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	var $actsAs = array(
		'GeoCoordinate'
	);

/**
 * BelongsTo association link
 *
 * @var array
 */
	var $belongsTo = array(
		'Zipcode' => array(
			'foreignKey' => false,
			'conditions' => array('Zipcode.zip = Address.zip')
		)
	);

/**
 * Validation rules
 *
 * @var array
 */
	var $validate = array(
		'name' => array(
			'rule' => array('custom', '/^[a-z0-9 ]*$/i'),
			'required' => false,
			'allowEmpty' => true,
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