<?php

App::import('Controller', 'SimpleCruds');

class CommentTypesController extends SimpleCrudsController {

	var $name = 'CommentTypes';
	
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