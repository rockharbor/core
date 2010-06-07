<?php

/**
 * Allows easy creation of Google Maps
 *
 * @author 		Jeremy Harris <jharris@rockharbor.org>
 * @package		app
 * @subpackage	app.views.helpers
 */
class GoogleMapHelper extends AppHelper {

/**
 * The JavaScript to output for this map
 *
 * @var array
 * @access protected
 */
	var $_buffer = array();
	
/**
 * Whether or not to create a div for the map or use a user-created div
 *
 * @var boolean
 * @access protected
 */
	var $_createDiv = true;
	
/**
 * The name of the map to be used for the div id and JavaScript variable name
 *
 * @var array
 * @access protected
 */
	var $_mapName = null;
	
/**
 * Google map types mapping
 *
 * @var array
 * @access protected
 */
	var $_mapTypesMap = array(
		'road' => 'google.maps.MapTypeId.ROADMAP'
	);

/**
 * Initial map zoom level
 *
 * @var integer
 * @access public
 */
	var $zoom = 4;
	
/**
 * Initial map center. Defaults to the average of all points.
 *
 * @var array
 * @access public
 */
	var $center = null;
	
/**
 * The initial map type to use. Supported types: road
 *
 * @var string
 * @access public
 */
	var $mapType = 'road';
	
/**
 * The element to use to create info windows
 *
 * @var string
 * @access public
 */
	var $infoWindowElement = 'google_map_info';

/**
 * Internal marker count
 *
 * @var integer
 * @access protected
 */	
	var $_markerCount = 0;
	
/**
 * Internal list of addresses
 *
 * @var array
 * @access protected
 */	
	var $_addresses = array();

/**
 * Extra helpers to load
 *
 * @var array
 * @access public
 */	
	var $helpers = array('Js', 'Html');

/**
 * Adds the JavaScript to load Google Maps
 *
 * If $mapName is null, GoogleMapsHelper will automatically create a unique map name and create
 * a div tag for the map. If it's not null, you'll need to add a div tag with that name so the
 * map can be created within it.
 *
 * #### Options:
 *	- boolean `inline` Whether the scripts should be created inline or buffered
 *	- boolean `sensor` The Google Map option "sensor" 
 *
 * @param string $mapName The name of the div to load the map into.
 * @param array $options Additional options
 * @access public
 * @return void Buffers scripts
 */
	function create($mapName = null, $options = array()) {		
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
		
		$options = array_merge($default, $options);
		return $this->Html->script('http://maps.google.com/maps/api/js?sensor='.($options['inline'] ? 'true': 'false'), array('inline' => $options['inline']));
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
 * @access public
 */
	function addAddresses($addresses = array()) {			
		foreach ($addresses as $address) {
			$this->_buffer[] = $this->_createMarker($address);
			$this->_addresses[] = $address;
		}
	}

/**
 * Finishes map creation, including outputting the buffered scripts
 *
 * @return mixed A div tag a mapName was not specified in GoogleMapHelper::create(), or null
 * @access public
 */
	function end() {	
		if (!$this->center) {
			// set center to average points
			$this->center = array(
				Set::apply('/lat', $this->_addresses, 'array_sum')/count($this->_addresses),
				Set::apply('/lng', $this->_addresses, 'array_sum')/count($this->_addresses)
			);
		}
		
		$mapName = $this->_mapName;
		$zoom = $this->zoom;
		$centerLat = $this->center[0];
		$centerLng = $this->center[1];
		$mapType = $this->_mapTypesMap[$this->mapType];		
		
		array_unshift($this->_buffer, "var $mapName = new google.maps.Map(document.getElementById('$mapName'), {
			zoom: $zoom,
			center: mapCenter,
			mapTypeId: $mapType
		});");
		array_unshift($this->_buffer, "var mapCenter = new google.maps.LatLng($centerLat, $centerLng);");
		
		foreach ($this->_buffer as $buffer) {
			$this->Js->buffer($buffer);
		}
		
		if ($this->_createDiv) {
			return $this->Html->tag('div', '', array(
				'id' => $mapName,
				'style' => 'width:100%;height:100%;min-width:600px;min-height:600px',
				'class' => 'google-map'
			));
		} else {
			return null;
		}
	}

/**
 * Creates the JavaScript for adding a marker to this map
 *
 * @param array $data The address data
 * @return string The JavaScript to buffer
 * @access protected
 */ 
	function _createMarker($data = array()) {
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
		
		$marker .= "var infowindow = new google.maps.InfoWindow();";
		
		if ($image || $name || $city || $state || $zip) {
			$content = ClassRegistry::getObject('view')->element($this->infoWindowElement, array('data' => $data));
			$marker .= "google.maps.event.addListener(marker$count, 'click', function() {
				infowindow.setContent('$content');
				infowindow.open($mapName, marker$count);
			});";
		}
		
		return $marker;
	}
}

?>