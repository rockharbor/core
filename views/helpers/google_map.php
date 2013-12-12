<?php
/**
 * Google map helper class.
 *
 * @copyright     Copyright 2014, ROCKHARBOR Church
 * @link          http://github.com/rockharbor
 * @package       google_maps
 * @subpackage    google_maps.views.helpers
 */

/**
 * GoogleMap Helper
 *
 * Allows easy creation of Google Maps
 *
 * @package       google_maps
 * @subpackage    google_maps.views.helpers
 * @todo Support static maps, add API key
 */
class GoogleMapHelper extends AppHelper {

/**
 * List of options, added on GoogleMapHelper::create()
 *
 * @var array
 */
	public $options = array();

/**
 * The JavaScript to output for this map
 *
 * @var array
 */
	protected $_buffer = array();

/**
 * Whether or not to create a div for the map or use a user-created div
 *
 * @var boolean
 */
	protected $_createDiv = true;

/**
 * The name of the map to be used for the div id and JavaScript variable name
 *
 * @var array
 */
	protected $_mapName = null;

/**
 * Google map types mapping
 *
 * @var array
 * @todo Add the rest of the map types
 */
	protected $_mapTypesMap = array(
		'road' => 'google.maps.MapTypeId.ROADMAP'
	);

/**
 * Initial map zoom level
 *
 * @var integer
 */
	public $zoom = 4;

/**
 * Initial map center. Defaults to the average of all points.
 *
 * @var array
 */
	public $center = null;

/**
 * The initial map type to use. Supported types: road
 *
 * @var string
 */
	public $mapType = 'road';

/**
 * The element to use to create info windows
 *
 * @var string
 */
	public $infoWindowElement = 'google_map_info';

/**
 * Internal marker count
 *
 * @var integer
 */
	protected $_markerCount = 0;

/**
 * Internal list of addresses
 *
 * @var array
 */
	protected $_addresses = array();

/**
 * Extra helpers to load
 *
 * @var array
 */
	public $helpers = array('Js', 'Html');

/**
 * Adds the JavaScript to load Google Maps
 *
 * If $mapName is null, GoogleMapsHelper will automatically create a unique map name and create
 * a div tag for the map. If it's not null, you'll need to add a div tag with that name so the
 * map can be created within it.
 *
 * ### Options:
 *	- boolean `inline` Whether the scripts should be created inline or buffered
 *	- boolean `sensor` The Google Map option "sensor"
 *
 * @param string $mapName The name of the div to load the map into.
 * @param array $options Additional options
 * @return void Buffers scripts
 */
	public function create($mapName = null, $options = array()) {
		$default = array(
			'inline' => false,
			'sensor' => false
		);

		if (!$mapName) {
			$mapName = 'gmap'.uniqid();
		} else {
			$this->_createDiv = false;
		}

		$this->_mapName = $mapName;

		$this->options = array_merge($default, $options);
		if (!$this->options['inline']) {
			$this->Js->buffer('
var script = document.createElement("script");
script.type = "text/javascript";
script.src = "//maps.googleapis.com/maps/api/js?sensor='.($this->options['sensor'] ? 'true': 'false').'&callback='.$mapName.'load";
document.body.appendChild(script);
');
			return null;
		}
		return $this->Html->script('//maps.google.com/maps/api/js?callback='.$mapName.'load&sensor='.($this->options['sensor'] ? 'true': 'false'), array('inline' => $this->options['inline']));
	}

/**
 * Adds addresses to the buffer
 *
 * Addresses contain the following information:
 * 'lat', 'lng', 'icon', 'image', 'name', 'street', 'city', 'state', 'zip'
 * All of which is optional except 'lat' and 'lng'
 *
 * @param array $addresses A list of addresses, including a `lat` and `lng`
 * @return void
 */
	public function addAddresses($addresses = array()) {
		if (!array_key_exists(0, $addresses)) {
			$addresses = array($addresses);
		}
		foreach ($addresses as $address) {
			if ($address['lat'] != 0 && $address['lng'] != 0) {
				$this->_buffer[] = $this->_createMarker($address);
				$this->_addresses[] = $address;
			}
		}
	}

/**
 * Finishes map creation, including outputting the buffered scripts
 *
 * @return mixed A div tag a mapName was not specified in GoogleMapHelper::create(), or null
 */
	public function end() {
		if (empty($this->_addresses)) {
			return null;
		}

		$center = $this->_center();
		$out = '';
		$mapName = $this->_mapName;
		$zoom = $this->zoom;
		$centerLat = $center[0];
		$centerLng = $center[1];
		$mapType = $this->_mapTypesMap[$this->mapType];

		array_unshift($this->_buffer, "var $mapName = new google.maps.Map(document.getElementById('$mapName'), {
			zoom: $zoom,
			center: mapCenter,
			mapTypeId: $mapType
		});");
		array_unshift($this->_buffer, "var mapCenter = new google.maps.LatLng($centerLat, $centerLng);");
		array_unshift($this->_buffer, "var infowindow = new google.maps.InfoWindow();");
		array_unshift($this->_buffer, 'window.'.$mapName.'load = function() {');
		array_push($this->_buffer, '}');

		foreach ($this->_buffer as $buffer) {
			$this->Js->buffer($buffer);
		}
		$this->_buffer = array();

		if ($this->options['inline']) {
			$out .= $this->Js->writeBuffer();
		}

		if ($this->_createDiv) {
			$out .= $this->Html->tag('div', '', array(
				'id' => $mapName,
				'style' => 'width:100%;height:100%;min-width:600px;min-height:600px',
				'class' => 'google-map'
			));
		}
		return $out;
	}

/**
 * Creates a static map
 *
 * @param integer $width The width of the map
 * @param integer $height The height of the map
 * @return string Html image code
 * @link http://code.google.com/apis/maps/documentation/staticmaps/
 */
	public function image($width = 200, $height = 200) {
		$center = $this->_center();

		$markers = array();
		foreach ($this->_addresses as $address) {
			$markers[] = $address['lat'].','.$address['lng'];
		}

		$query = http_build_query(array(
			'center' => $center[0].','.$center[1],
			'zoom' => $this->zoom,
			'size' => $width.'x'.$height,
			'maptype' => $this->mapType,
			'sensor' => 'false',
			'markers' => implode('|', $markers)
		));

		$alt = 'Google Map';
		return $this->Html->image('http://maps.google.com/maps/api/staticmap?'.$query, compact('alt', 'width', 'height'));
	}

/**
 * Resets the GoogleMapHelper back to its original state
 */
	public function reset() {
		$this->center = null;
		$this->_addresses = array();
		$this->_buffer = array();
		$this->_markerCount = 0;
		$this->_mapName = null;
		$this->_createDiv = true;
		$this->zoom = 4;
		$this->mapType = 'road';
		$this->infoWindowElement = 'google_map_info';
	}

/**
 * Creates the JavaScript for adding a marker to this map
 *
 * @param array $data The address data
 * @return string The JavaScript to buffer
 */
	protected function _createMarker($data = array()) {
		if (empty($data)) {
			return false;
		}

		$default = array(
			'lat' => null,
			'lng' => null,
			'icon' => null,
			'image' => null,
			'name' => null,
			'street' => null,
			'city' => null,
			'state' => null,
			'zip' => null
		);

		$data = array_merge($default, $data);

		$count = $this->_markerCount++;
		$mapName = $this->_mapName;

		extract($data);

		if (!$lat || !$lng) {
			$message = "GoogleMapHelper::_createMarker - ";
			$message .= "You must at least provide the keys 'lat' and 'lng' in your address.";
			trigger_error($message, E_USER_NOTICE);
			return false;
		}
		$marker = "var point$count = new google.maps.LatLng($lat, $lng);";
		$marker .= "var marker$count = new google.maps.Marker({";
		if (!is_null($icon)) {
			$marker .= "icon: '$icon',";
		}
		$marker .= "position: point$count, map: $mapName});";

		if ($image || $name || $city || $state || $zip) {
			$View = ClassRegistry::getObject('view');
			// trick debug_kit into not rendering the comments before and after the element
			$View->params['url']['ext'] = 'gm';
			$content = $this->Html->tag('div', $View->element($this->infoWindowElement, array('data' => $data)));
			$marker .= "google.maps.event.addListener(marker$count, 'click', function() {
				infowindow.setContent('$content');
				infowindow.open($mapName, marker$count);
			});";
		}

		return $marker;
	}

/**
 * Returns a numerically indexed array of the latitude and longitude, based on
 * user-defined center or the average of the buffered addresses
 *
 * @return array
 */
	protected function _center() {
		if (!$this->center) {
			// set center to average points
			$this->center = array(
				Set::apply('/lat', $this->_addresses, 'array_sum')/count($this->_addresses),
				Set::apply('/lng', $this->_addresses, 'array_sum')/count($this->_addresses)
			);
		}
		return $this->center;
	}
}

