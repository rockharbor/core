<?php

App::import('Controller', 'SimpleCruds');

class RosterStatusesController extends SimpleCrudsController {

	var $name = 'RosterStatuses';
	
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