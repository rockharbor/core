<?php
/**
 * School controller class.
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
 * Schools Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class SchoolsController extends SimpleCrudsController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Schools';

/**
 * Components
 *
 * @var array
 */
	var $components = array(
		'FilterPagination'
	);
	
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

	function index() {
		$this->viewPath = 'schools';
		$this->set('types', $this->School->types);
		$this->FilterPagination->startEmpty = false;
		if (!empty($this->data)) {
			$this->paginate = array(
				'conditions' => array(
					'School.type' => $this->data['School']['type']
				)
			);
		}
		$this->set('schools', $this->FilterPagination->paginate());
	}
}
?>