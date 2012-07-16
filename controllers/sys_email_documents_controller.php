<?php
/**
 * Sys Email Document controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Includes
 */
App::import('Controller', 'Documents');

/**
 * SysEmailDocuments Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class SysEmailDocumentsController extends DocumentsController {

/**
 * The name of the model to associate the Document with
 * 
 * @var string
 */
	var $model = 'SysEmail';

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
		$this->modelId = isset($this->passedArgs[$this->model]) ? $this->passedArgs[$this->model] : null;
	}
	
}
