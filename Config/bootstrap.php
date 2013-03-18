<?php

/**
 * Cache configuration
 */
Cache::config('default', array(
	'engine' => 'File'
));
Cache::config('acl', array(
	'engine' => 'File',
	'prefix' => 'core_acl_',
	'path' => CACHE . 'acl',
	'duration' => 3600,
	'serialize' => true
));

/**
 * Bring in Core configuration class
 */
require_once APP.'libs'.DS.'core.php';

/**
 * Bring in and overwrite (specific) Media plugin settings
 */
require_once APP.'plugins'.DS.'media'.DS.'config'.DS.'core.php';

/**
 * Custom filters based on model
 */
Configure::write('Core.mediafilters.user', array(
	's'   => array('convert' => 'image/png', 'fitCrop' => array(60, 60)),
	'm'   => array('convert' => 'image/png', 'fitCrop' => array(90, 90)),
));
Configure::write('Core.mediafilters.involvement', array(
	's'   => array('convert' => 'image/png', 'fitCrop' => array(60, 60)),
	'm'   => array('convert' => 'image/png', 'fitCrop' => array(260, 90)),
));
Configure::write('Core.mediafilters.ministry', array(
	's'   => array('convert' => 'image/png', 'fitCrop' => array(60, 60)),
	'm'   => array('convert' => 'image/png', 'fitCrop' => array(260, 90)),
));
// fallback defaults
Configure::write('Core.mediafilters.default', array(
	's'   => array('convert' => 'image/png', 'fitCrop' => array(60, 60)),
	'm'   => array('convert' => 'image/png', 'fitCrop' => array(90, 90)),
));

/**
 * Set QueueEmail to save the emails in the db
 */
Configure::write('QueueEmail.deleteAfter', false);

/**
 * Converts `<br />`'s to newlines
 *
 * @param string $input
 * @return string
 */
function br2nl($input) {
	return preg_replace('/<br(\s+)?\/?>/i', PHP_EOL, $input);
}

/**
 * Recusively runs `array_filter` on an array, removing empty keys
 *
 * @param array $data
 * @return array
 */
function array_filter_recursive($data = array()) {
	$item = $data + array();
	return array_filter($item, '_array_filter_recursive_callback');
}

/**
 * Callback for `array_filter_recursive
 * `
 * @param mixed $item
 * @return mixed
 */
function _array_filter_recursive_callback(&$item) {
	if (is_array($item)) {
		$item = array_filter($item, '_array_filter_recursive_callback');
		return !empty($item);
	}
	if (!empty($item)) {
		return true;
	}
}