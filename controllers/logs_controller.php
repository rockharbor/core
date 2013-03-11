<?php
/**
 * Log controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Logs Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class LogsController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	public $name = 'Logs';

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 */
	public function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * Shows a list of logs
 */
	public function index() {
		$this->Log->recursive = 0;
		$this->set('logs', $this->paginate());
	}
}
