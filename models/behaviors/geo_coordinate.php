<?php

/**
 * Geocordinate behavior class
 *
 * @copyright	Copyright 2014, ROCKHARBOR Church
 * @link		http://github.com/rockharbor
 * @package		google_maps
 * @subpackage	google_maps.models.behavior
 */

/**
 * Includes
 */
App::import('Core', 'HttpSocket');

/**
 * Geocoordinate Behavior
 *
 * Automatically geocodes address data and saves data when an
 * address is created/updated
 *
 * Pass data as an array
 * array(
 *	'Model' => array(
 *		'address_line_1' => '123 Main St.'
 *		'city' => 'Anytown',
 *		'state' => 'CA',
 *		'zip' => '12345'
 *	)
 * );
 * OR array('Model' => '123 Main St., Anytown, CA, 12345');
 *
 * Model needs `lat` and `lng` fields.
 *
 * @package       google_maps
 * @subpackage    google_maps.models.behaviors
 * @todo Allow more flexibility in fields, don't be so strict when saving
 */
class GeoCoordinateBehavior extends ModelBehavior {

/**
 * Settings
 *
 * Service details and settings
 *
 * @var array
*/
	public $settings = array(
		'providers' => array(
			'google'	=> array(
				'name'		=> 'Google Maps',
				'version'	=> 3,
				'api_key'	=> '',
				'url'		=> 'https://maps.googleapis.com/maps/api/geocode/:format?address=:q&sensor=false&region=:region',
				'formats'	=> array('json', 'xml')
			)
		),
		'settings'	=> array(
			'provider'		=> 'google',
			'region'	=> 'US',
			'format'		=> 'json'
		)
	);

/**
 * Start the behavior
 *
 * @param object $Model Model reference
 */
	public function setup(&$Model) {
		$this->connection = new HttpSocket();
	}

/**
 * Adds the lat and lng to the address
 *
 * @param object $Model Model reference
 */
	public function beforeSave(&$Model) {
		if (isset($Model->data[$Model->alias]['address_line_1']) && !empty($Model->data[$Model->alias]['address_line_1']) &&
			isset($Model->data[$Model->alias]['city']) && !empty($Model->data[$Model->alias]['city']) &&
			isset($Model->data[$Model->alias]['state']) && !empty($Model->data[$Model->alias]['state']) &&
			isset($Model->data[$Model->alias]['zip']) && !empty($Model->data[$Model->alias]['zip'])
		) {
			// get geo coords
			$coords = $this->_geocoords($Model->data[$Model->alias]);

			// add to data
			if (isset($coords['lat']) && isset($coords['lng'])) {
				$Model->data[$Model->alias]['lat'] = $coords['lat'];
				$Model->data[$Model->alias]['lng'] = $coords['lng'];
			}
		}

		// continue with save
		return true;
	}

/**
 * Returns latitude and longitude using default provider
 *
 * @param object $Model Model reference
 * @param mixed $q Query, an address array or string
 * @return array Lat and lng
 */
	public function geoCoordinates(&$Model, $q = '') {
		// get geo coords
		return $this->_geocoords($q);
	}

/**
* Get Lng/Lat from provider
*
* @param mixed $query Query
* @return array
*/
	private function _geocoords($query) {
		if (is_array($query)) {
			$q = $query['address_line_1'];
			if (isset($query['address_line_2'])) {
				$q .= $query['address_line_2'];
			}
			$q .= ', '.$query['city'].', '.$query['state'].' '.$query['zip'];
		} else {
			$q = $query;
		}

		$data = array();

		//Extract variables to use
		extract($this->settings['settings']);
		extract($this->settings['providers'][$provider]);

		if (!in_array($format, $formats)) {
			//provider doesn't support format
			return $data;
		}

		//Build url
		$url = String::insert($url,compact('format','q','region'));

		//Get data and parse
		if($response = $this->connection->get($url)) {
			$json = json_decode($response);
			if ($json->status == 'OK' && isset($json->results[0])) {
				$result = $json->results[0];
				$data['lat'] = $result->geometry->location->lat;
				$data['lng'] = $result->geometry->location->lng;
				$data['address1'] = $result->formatted_address;
			}
		}

		return $data;
	}
}
