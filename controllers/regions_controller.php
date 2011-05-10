<?php
/**
 * Region controller class.
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
 * Regions Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class RegionsController extends SimpleCrudsController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Regions';
	
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

/**
 * Shows a list of regions
 */
	function index() {
		$this->viewPath = 'regions';
		
		$this->Region->recursive = 1;
		$this->set('regions', $this->paginate()); 
	}

}
?>