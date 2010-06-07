<?php

App::import('Controller', 'SimpleCruds');

class InterestListTypesController extends SimpleCrudsController {

	var $name = 'InterestListTypes';
	
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