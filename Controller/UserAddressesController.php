<?php
/**
 * User Address controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Includes
 */
App::uses('AddressesController', 'Controller');

/**
 * UserAddresses Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class UserAddressesController extends AddressesController {

/**
 * The name of the model to associate the Address with
 *
 * @var string
 */
	public $model = 'User';

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->modelId = isset($this->passedArgs[$this->model]) ? $this->passedArgs[$this->model] : null;
	}

}
