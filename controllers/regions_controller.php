<?php

App::import('Controller', 'SimpleCruds');

class RegionsController extends SimpleCrudsController {

	var $name = 'Regions';
	
/**
 * Model::beforeFilter() callback
 *
 * Sets permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
	}
	
	function index() {
		$this->viewPath = 'regions';
		
		$this->Region->recursive = 1;
		$this->set('regions', $this->paginate()); 
	}

}
?>