<?php
/**
 * Involvement Document controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Includes
 */
App::uses('DocumentsController', 'Controller');

/**
 * InvolvementDocuments Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class InvolvementDocumentsController extends DocumentsController {

/**
 * The name of the model to associate the Document with
 *
 * @var string
 */
	public $model = 'Involvement';

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
