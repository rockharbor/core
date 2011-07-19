<?php
/**
 * Ministry Leader controller class.
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
 * MinistryLeaders Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class MinistryLeadersController extends LeadersController {

/**
 * The name of the model to associate the Leader with
 *
 * @var string
 */
	var $model = 'Ministry';

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

/**
 * Shows a manager dashboard
 *
 * @see LeadersController::dashboard()
 */
	function dashboard() {
		$this->viewPath = 'ministry_leaders';
		$this->paginate = array(
			'conditions' => array(
				'Leader.model' => 'Ministry',
				'Leader.user_id' => $this->passedArgs['User']
			),
			'contain' => array(
				'Ministry' => array(
					'Role',
					'ParentMinistry',
					'Campus'
				)
			)
		);
		$leaders = $this->paginate();
		$this->set('leaders', $leaders);
		$this->set('model', $this->model);
	}
	
}
?>