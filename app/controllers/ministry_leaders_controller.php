<?php

App::import('Controller', 'Leaders');

class MinistryLeadersController extends LeadersController {

	var $model = 'Ministry';

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