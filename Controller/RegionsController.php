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
App::uses('SimpleCrudsController', 'Controller');

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
	public $name = 'Regions';

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 */
	public function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * Shows a list of regions
 */
	public function index() {
		$this->viewPath = 'regions';

		$this->Region->recursive = 1;
		$this->set('regions', $this->paginate());
	}

}
