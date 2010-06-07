<?php

App::import('Controller', 'SimpleCruds');

class RolesController extends SimpleCrudsController {

	var $name = 'Roles';
	
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