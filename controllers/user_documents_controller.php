<?php

App::import('Controller', 'Documents');

class UserDocumentsController extends DocumentsController {

	var $model = 'User';

/**
 * Model::beforeFilter() callback
 *
 * Sets permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
		$this->modelId = isset($this->passedArgs[$this->model]) ? $this->passedArgs[$this->model] : null;
	}
	
}

?>