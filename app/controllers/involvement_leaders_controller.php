<?php
/**
 * Involvement Leader controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Includes
 */
App::import('Controller', 'Leaders');

/**
 * InvolvementLeaders Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class InvolvementLeadersController extends LeadersController {

/**
 * The name of the model to associate the Leader with
 *
 * @var string
 */
	var $model = 'Involvement';

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */
	function beforeFilter() {
		parent::beforeFilter();
		$this->modelId = isset($this->passedArgs[$this->model]) ? $this->passedArgs[$this->model] : null;
	}
	
}
?>