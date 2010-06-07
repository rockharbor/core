<?php

App::import('Controller', 'SimpleCruds');

class SchoolsController extends SimpleCrudsController {

	var $name = 'Schools';
	
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

}
?>