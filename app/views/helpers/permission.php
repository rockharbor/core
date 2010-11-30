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
 * Stored app controller for PermissionHelper::check()
 *
 * @var Controller
 */
	var $AppController = null;

/**
 * Grabs permissions set in the controller
 *
 * {{{
 * $this->set('_canSeeThisThing', true);
 * }}}
 *
 * And attaches them to the helper as
 *
 * {{{
 * $Permission->canSeeThisThing
 * }}}
 *
 * Automatically denies permission for missing permissions
 *
 * @param string $name The name of the missing permission
 * @return false
 */
	function  __get($name) {
		if (isset($this->{'_'.$name})) {
			return $this->{'_'.$name};
		}
		CakeLog::write('Auth', 'Missing permission check for "'.$name.'"');
		return false;
	}

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
 * Checks if the logged in user has access to a controller/action path
 *
 * @param string $path The controller/action path
 * @return boolean
 */
	function check($path = '') {
		if (empty($path)) {
			return false;
		}
		if (is_array($path)) {
			$path = Router::url($path);
		}
		$view =& ClassRegistry::getObject('view');
		if (!$this->AppController) {
			App::import('Controller', 'App');
			$this->AppController = new AppController();
			$this->AppController->constructClasses();
		}
		$this->AppController->activeUser = $view->viewVars['activeUser'];
		return $this->AppController->isAuthorized($path);
	}
}