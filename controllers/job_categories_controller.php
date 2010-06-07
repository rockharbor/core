<?php

App::import('Controller', 'SimpleCruds');

class JobCategoriesController extends SimpleCrudsController {

	var $name = 'JobCategories';
	
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