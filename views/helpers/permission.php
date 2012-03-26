<?php
/**
 * Permission helper class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.views.helpers
 */

/**
 * Permission Helper
 *
 * Stores permissions from the controller for access in the views. No,
 * PermissionHelper::check() is not very MVC but it makes the most sense to
 * place it here.
 *
 * @package       core
 * @subpackage    core.app.views.helpers
 */
class PermissionHelper extends AppHelper {

/**
 * Stored controllers for `check()`
 *
 * @var Controller
 */
	var $controllers = array();

/**
 * Additional helpers needed by this helper
 *
 * @var array
 */
	var $helpers = array(
		'Html',
		'Js'
	);
	
/**
 * Whether the user can see private items or not
 * 
 * @var boolean
 */
	var $_canSeePrivate = null;

/**
 * Takes all vars named _can{DoSomething} set on the view and saves them as a
 * permission and removes them from the view vars
 */
	function beforeRender() {
		$view =& ClassRegistry::getObject('view');
		if ($view === false) {
			return;
		}
		foreach ($view->viewVars as $varName => $value) {
			if (strpos($varName, '_can') !== false) {
				$this->{$varName} = $value;
				unset($view->viewVars[$varName]);
			}
		}
	}

/**
 * Creates a link if the user is authorized to access it. Tries to determine
 * if it should be an HTML or JavaScript link
 *
 * @param string $title The link title
 * @param array $url Only accepts cake-based url and NEEDS controller defined
 * @param array $options Options to pass to link
 * @param string $confirmMessage A javascript confirm message
 * @return string The link
 */
	function link($title, $url = null, $options = array(), $confirmMessage = false) {
		if (is_string($url)) {
			$url = Router::parse($url);
		}
		if ($this->check($url)) {
			$helper = 'Html';
			$hasJs = array_intersect(array('update', 'success', 'complete', 'beforeSend', 'error'), array_keys($options));
			if (!empty($hasJs)) {
				$helper = 'Js';
			}
			return $this->{$helper}->link($title, $url, $options, $confirmMessage);
		}
		return null;
	}

/**
 * Checks if the logged in user has access to a controller/action path
 *
 * @param array $path The url to check
 * @param array $user The user to check
 * @return boolean
 * @see AppController::isAuthorized()
 */
	function check($path = array()) {
		if (empty($path)) {
			return false;
		}
		$params = array_diff_key($path, array('plugin' => null, 'controller' => null, 'action' => null));
		if (empty($path['controller'])) {
			$path['controller'] = $this->params['controller'];
		}
		if (!isset($path['action'])) {
			$path['action'] = 'index';
		}
		if (!isset($path['plugin']) || !$path['plugin']) {
			$path['plugin'] = $this->plugin;
		}
		
		$view =& ClassRegistry::getObject('view');
		$controller = $path['controller'];
		if (!isset($this->controllers[$controller])) {
			$import = Inflector::camelize($controller);
			$classname = $import.'Controller';
			if ($path['plugin']) {
				$import = Inflector::camelize($path['plugin']).'.'.$import;
			}
			App::import('Controller', $import);
			$this->controllers[$controller] = new $classname();
			$this->controllers[$controller]->__construct();
			$this->controllers[$controller]->constructClasses();
			$this->controllers[$controller]->beforeFilter();
		}
		$url = Router::url($path);
		return 
			in_array($path['action'], $this->controllers[$controller]->Auth->allowedActions) ||
			$this->controllers[$controller]->isAuthorized($url, $params, $view->viewVars['activeUser']);
	}
	
/**
 * Checks if a user is allowed to see private items
 * 
 * @return boolean
 */
	function canSeePrivate() {
		if ($this->_canSeePrivate === null) {
			$view =& ClassRegistry::getObject('view');
			$currentUserGroup = $view->viewVars['activeUser']['Group']['id'];
			$Group = ClassRegistry::init('Group');
			$this->_canSeePrivate = $Group->canSeePrivate($currentUserGroup);
		}
		return $this->_canSeePrivate;
	}
}