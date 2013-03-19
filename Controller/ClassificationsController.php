<?php
/**
 * Classification controller class.
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
 * Classifications Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */

class ClassificationsController extends SimpleCrudsController {

/**
 * The name of the controller
 *
 * @var string
 */
	public $name = 'Classifications';

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 */
	public function beforeFilter() {
		parent::beforeFilter();
	}

}
