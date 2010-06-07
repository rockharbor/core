<?php

App::import('Controller', 'SimpleCruds');

class PaymentTypesController extends SimpleCrudsController {

	var $name = 'PaymentTypes';
	
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