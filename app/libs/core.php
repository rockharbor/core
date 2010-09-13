<?php
/**
 * Core app configuration class
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.libs
 */

/**
 * Core class
 *
 * Similar to the Configuration class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.libs
 */
class Core {

/**
 * Core version
 *
 * @var string
 * @access protected
 */
	var $_version = '2.0.0-alpha';

/**
 * Stored settings
 *
 * @var array
 */
	var $settings = array();

/**
 * Loads settings into config and stores them in cache
 *
 * @param boolean $force Force re-writing cache
 * @return void
 */
	function loadSettings($force = false) {
		$self =& Core::getInstance();
		
		$settings = $self->_loadDbSettings($force);
		foreach ($settings as $setting => $value) {
			$self->_write($setting, $value);
		}
	}

/**
 * Returns a singleton instance
 * 
 * @return Core class object
 */
	function &getInstance() {
		static $instance = array();
		if (!$instance) {
			$instance[0] =& new Core();
			$instance[0]->loadSettings();
		}
		return $instance[0];	
	}

/**
 * Reads a setting. If $var is 'version' it will return the current version
 * of Core. If $var is empty, it will return a key-value array of all settings.
 *
 * @param string $var The key to read
 * @return mixed The setting value or all settings
 */
	function read($var = '') {
		$self =& Core::getInstance();
		if ($var == 'version' || $var == '_version') {
			return $self->_version;
		}
		if (empty($var)) {
			return $self->settings;
		}
		$keys = explode('.', $var);
		if (isset($self->settings[$keys[0]])) {
			$var = $self->settings[$keys[0]];
			array_shift($keys);
			foreach ($keys as $k) {
				if (isset($var[$k])) {
					$var = $var[$k];
				} else {
					return null;
				}
			}
			return $var;
		}
		return null;
	}

/**
 * Writes a setting to configuration (not to the db!)
 * 
 * @param mixed $key The key to write
 * @param mixed $value The value to write
 * @return mixed The variable
 * @access protected
 */
	function _write($key, $value) {
		$self =& Core::getInstance();
		$keys = explode('.', $key);
		$keys = array_reverse($keys);
		if (count($keys) == 1) {
			$self->settings[$keys[0]] = $value;
			return $self->settings[$keys[0]];
		}
		$child = array(
			$keys[0] => $value
		);
		array_shift($keys);		
		foreach ($keys as $k) {
			$child = array(
				$k => $child
			);
		}
		$var = key($child);
		if (!isset($self->settings[$var])) {
			$self->settings[$var] = $child[$var];
		} else {
			$self->settings[$var] = Set::merge($self->settings[$var], $child[$var]);
		}
		return $self->settings[$var];
	}

/**
 * Loads settings from the database. Caching is taken care of by Cacher.Cache
 *
 * @param boolean $force Force re-writing cache
 * @return array Array of app settings
 */
	function _loadDbSettings($force = false) {
		App::import('Model', 'AppSetting');
		$AppSetting = ClassRegistry::init('AppSetting');
		if ($force) {
			$AppSetting->clearCache();
		}
		$appSettings = $AppSetting->find('all');
		// add tagless versions of the html tagged ones
		$tagless = array();
		foreach ($appSettings as $appSetting) {
			if ($appSetting['AppSetting']['type'] == 'html') {
				$tagless[] = array(
					'AppSetting' => array(
						'name' => $appSetting['AppSetting']['name'].'_tagless',
						'value' => strip_tags($appSetting['AppSetting']['value'])
					)
				);
			}
		}
		$appSettings = array_merge($appSettings, $tagless);
		$appSettings = Set::combine($appSettings, '{n}.AppSetting.name', '{n}.AppSetting.value');

		return $appSettings;
	}

}

?>