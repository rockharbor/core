<?php
/**
 * Confirm behavior class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models.behaviors
 */

/**
 * Includes
 */
App::import('Core', 'HttpSocket');

/**
 * Confirm Behavior
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
 * @package       core
 * @subpackage    core.app.models.behaviors
 * @todo Allow more flexibility in fields, don't be so strict when saving,
 *		wrap into plugin and include the helper
 */
class GeoCoordinateBehavior extends ModelBehavior {

/**
 * Providers
 *
 * Provider details and settings to parse details
 *
 * @var array
*/
	public $providers = array(
		'google'    => array(
			'enabled'   => true,
			'api'       => 'ABQIAAAA09mnl0ou-zdXazrIvNToJBQrdm0PJNmhoodn2MySO_Nz62fdfBSTZCuUWPHpElUVD7Mt5etMpfke-Q',
			'url'       => 'http://maps.google.com/maps/geo?q=:q&output=xml&key=:api',
			'fields'    => array(
				'lng'       => '/<coordinates>(.*?),/',
				'lat'       => '/,(.*?),[^,\s]+<\/coordinates>/',
				'address1'  => '/<address>(.*?)<\/address>/',
				'postcode'  =>  '/<PostalCodeNumber>(.*?)<\/PostalCodeNumber>/',
				'country'   =>  '/<CountryNameCode>(.*?)<\/CountryNameCode>/'
			)
		)
	);

/**
 * Settings
 *
 * @var string
*/
	public $settings = array(
		'provider' => 'google',
		'countryCode' => 'US'
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
			$coords = $this->_geocoords($Model->data[$Model->alias], $this->settings);

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
		return $this->_geocoords($q, $this->settings);
	}

/**
* Get Lng/Lat from provider
*
* @param mixed $query Query
* @param array $options Options
* @return array
*/
	private function _geocoords($query, $options = array()) {
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
		extract($options);
		extract($this->providers[$provider]);

		//Add country code to query
		$q .= ', '.$countryCode;

		//Build url
		$url = String::insert($url,compact('api','q','countryCode'));

		//Get data and parse
		if($result = $this->connection->get($url)) {
			foreach($fields as $field => $regex) {
				if(preg_match($regex,$result,$match)) {
					if(!empty($match[1]))
						$data[$field] = $match[1];
				}
			}
		}

		return $data;
	}
}
