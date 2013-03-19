<?php
/**
 * Involvement Type controller class.
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
 * InvolvementTypes Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class InvolvementTypesController extends SimpleCrudsController {

/**
 * The name of the controller
 *
 * @var string
 */
	public $name = 'InvolvementTypes';

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 */
	public function beforeFilter() {
		parent::beforeFilter();
	}

}
