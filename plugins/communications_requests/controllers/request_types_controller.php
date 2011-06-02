<?php
/**
 * Request Types controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       communications_requests
 * @subpackage    communications_requests.controllers
 */

/**
 * Imports
 */
App::import('Controller', 'SimpleCruds');

/**
 * RequestTypes Controller
 *
 * @package       communications_requests
 * @subpackage    communications_requests.controllers
 */
class RequestTypesController extends SimpleCrudsController {

/**
 * Extra helpers for this controller
 * 
 * @var array
 */
	var $helpers = array(
		'Formatting'
	);
	
/**
 * Shows a list of request types and the people to be notified
 */
	function index() {
		$this->viewPath = 'request_types';
		$this->paginate = array(
			'contain' => array(
				'RequestNotifier' => array(
					'User' => array(
						'Profile' => array(
							'fields' => array('name')
						)
					)
				)
			)
		);
		parent::index();
	}
	
}