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
 * Imports
 */
App::import('Core', 'Router');

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
 * The Acl Component
 * 
 * @var AclComponent
 */
	var $Acl = null;

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
			$instance[0]->_initAcl();
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
		foreach ($appSettings as &$appSetting) {
			if ($appSetting['AppSetting']['type'] == 'html') {
				$tagless[] = array(
					'AppSetting' => array(
						'name' => $appSetting['AppSetting']['name'].'_tagless',
						'value' => strip_tags($appSetting['AppSetting']['value'])
					)
				);
			} elseif ($appSetting['AppSetting']['type'] == 'image' && isset($appSetting['Image']['id'])) {
				$appSetting['AppSetting']['value'] = $appSetting['Image'];
			}
		}
		$appSettings = array_merge($appSettings, $tagless);
		$appSettings = Set::combine($appSettings, '{n}.AppSetting.name', '{n}.AppSetting.value');

		return $appSettings;
	}

/**
 * Initializes the AclComponent
 */
	function _initAcl() {
		$self =& Core::getInstance();
		if (!$self->Acl) {
			App::import('Component', 'Acl');
			$self->Acl = new AclComponent();
		}
	}

/**
 * Adds an ACO to allow a group access to an action
 * 
 * @param string $action The action to allow
 * @param int $foreign_key The group id. Default is 8 (User)
 * @return boolean Success 
 */
	function addAco($action = null, $foreign_key = 8) {
		if (!$action) {
			return false;
		}
		$self =& Core::getInstance();
		$model = 'Group';
		if (stripos($action, 'controllers/') === false) {
			$action = 'controllers/'.ltrim($action, '/');
		}
		
		// iterate through the path and add missing acos
		$nodes = explode('/', $action);
		$parentId = null;
		$path = '';
		foreach ($nodes as $node) {
			$path .= $node;
			$acoNode = $self->Acl->Aco->node($path);
			if (!$acoNode) {
				$self->Acl->Aco->create(array('parent_id' => $parentId, 'model' => null, 'alias' => $node));
				$acoNode = $self->Acl->Aco->save();
				$parentId = $self->Acl->Aco->id;
			} else {
				$parentId = $acoNode[0]['Aco']['id'];
			}
			$path .= '/';
		}
		
		// save the permission
		return $self->Acl->allow(compact('model', 'foreign_key'), $action);
	}

/**
 * Removes an ACO record
 * 
 * @param string $action
 * @param type $foreign_key The group id. Default is 8 (User)
 * @return boolean Success
 */
	function removeAco($action = null, $foreign_key = 8) {
		$self =& Core::getInstance();
		$model = 'Group';
		if (stripos($action, 'controllers/') === false) {
			$action = 'controllers/'.ltrim($action, '/');
		}
		$key = md5(serialize(compact('model', 'foreign_key', 'action')).'main');
		Cache::delete($key, 'acl');
		
		$acoNode = $self->Acl->Aco->node($action);
        if (isset($acoNode['0']['Aco']['id'])) {
           return  $self->Acl->Aco->delete($acoNode[0]['Aco']['id']);
        }
	}
	
/**
 * Checks acl for a certain group and action. It will cache the result so checking
 * again should have less of a hit
 * 
 * @param int $foreign_key The group id
 * @param string $action The Acl action to check
 * @param string $type This key is only here to differentiate between main and 
 *		conditional access. It acts as a way of namespacing the cache so checking
 *		the same action for main access vs conditional access will return proper
 *		results
 */
	function acl($foreign_key = 8, $action = '/', $type = 'main') {
		$self =& Core::getInstance();
		$model = 'Group';
		if (stripos($action, 'controllers/') === false) {
			$action = 'controllers/'.ltrim($action, '/');
		}
		$key = md5(serialize(compact('model', 'foreign_key', 'action')).$type);
		if (Cache::read($key, 'acl') !== false) {
			$access = Cache::read($key, 'acl');
		} else {
			$access = $self->Acl->check(compact('model', 'foreign_key'), $action);
			Cache::write($key, $access, 'acl');
		}
		if (!$access) {
			$message = "User of group $foreign_key trying to access $action without permission.";
			CakeLog::write('auth', $message);
		}
		return $access;
	}

/**
 * Hooks into CORE to allow adding links to your plugin. Links are only shown if 
 * the user has permission to see them
 * 
 * #### Options:
 * - string $title The link title
 * - string $element The element to use instead of a link
 * - array $options Options for the link
 * 
 * @param mixed $url String or array url
 * @param string $area Dot-string representing the area of the app we want to hook
 *   into. To hook into the top-level nav, use `root`
 * @param array $options Hook options
 * @return void
 */
	function hook($url = array(), $area = null, $options = array()) {
		if (empty($url)) {
			return;
		}
		if (is_array($url)) {
			if (!isset($url['action'])) {
				$last = $url['controller'];
			} else {
				$last = $url['action'];
			}
		} else {
			$last = explode('/', $url);
			$last = array_pop($last);
		}
		$_defaults = array(
			'title' => Inflector::humanize($last),
			'element' => null,
			'options' => array()
		);
		$options = array_merge($_defaults, $options);
		extract($options);
		
		$area = trim($area, '.');
		$self =& Core::getInstance();
		$existing = $self->getHooks($area);
		if (!$existing) {
			$area .= '.options';
		}
		$existing = compact('url', 'group', 'operator', 'title', 'element', 'options');
		$self->_write('hooks.'.$area, $existing);
	}
	
/**
 * Gets hooks for an area
 * 
 * @param string $area 
 * @param array $exclude Sub items to exclude
 * @return array The area's hooks
 */
	function getHooks($area = null, $exclude = array()) {
		$area = trim($area, '.');
		$self =& Core::getInstance();
		$hooks = (array)$self->read('hooks.'.$area);
		return array_diff_key($hooks, Set::normalize($exclude));
	}
}

?>