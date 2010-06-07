<?php

App::import('Controller', 'Documents');

class SysEmailDocumentsController extends DocumentsController {

	var $model = 'SysEmail';

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