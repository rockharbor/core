<?php
/**
 * Payment Type controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Includes
 */
App::import('Controller', 'SimpleCruds');

/**
 * PaymentTypes Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class PaymentTypesController extends SimpleCrudsController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'PaymentTypes';
	
/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
	}

}
?>