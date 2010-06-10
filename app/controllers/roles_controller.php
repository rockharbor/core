<?php
/**
 * Address controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Includes
 */
App::import('Controller', 'SimpleCruds');

/**
 * Addresses Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 * @todo Remove from SimpleCruds and restrict to ministry manager, campus manager
 */
class RolesController extends SimpleCrudsController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Roles';
	
/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
	}

}
?>