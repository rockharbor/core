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
 */
class RolesController extends AppController {

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

/**
 * Adds a role to a ministry
 */
	function add() {
		if (!empty($this->data)) {
			$this->Role->create();
			if ($this->Role->save($this->data)) {
				$this->Session->setFlash('The Role has been added', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('The Role could not be added. Please, try again.', 'flash'.DS.'failure');
			}
		}
		$this->set('ministry', $this->Role->Ministry->read(null, $this->passedArgs['Ministry']));
	}

}
?>