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
	public $name = 'Schools';

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'FilterPagination'
	);

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 */
	public function beforeFilter() {
		$this->set('types', $this->School->types);
		parent::beforeFilter();
	}

	public function index() {
		$this->viewPath = 'schools';
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
