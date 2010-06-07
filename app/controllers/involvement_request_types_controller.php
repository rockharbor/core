<?php

App::import('Controller', 'SimpleCruds');

class InvolvementRequestTypesController extends SimpleCrudsController {

	var $name = 'InvolvementRequestTypes';
	
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