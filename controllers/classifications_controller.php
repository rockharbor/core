<?php

App::import('Controller', 'SimpleCruds');

class ClassificationsController extends SimpleCrudsController {

	var $name = 'Classifications';
	
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