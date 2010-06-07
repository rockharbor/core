<?php

App::import('Controller', 'SimpleCruds');

class InvolvementTypesController extends SimpleCrudsController {

	var $name = 'InvolvementTypes';
	
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