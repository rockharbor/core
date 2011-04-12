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
		parent::dashboard();
		// we attach extra info in this way because of how the leader model is
		// bound to other models can break contain queries
		$leaders = $this->viewVars['leaders'];
		foreach ($leaders as &$leader) {
			$roles = $this->Leader->Ministry->Role->find('all', array(
				'conditions' => array(
					'Role.ministry_id' => $leader['Ministry']['id']
				)
			));
			$roles = array('Role' => Set::extract('/Role/.', $roles));
			$leader = Set::merge($leader, $roles);
		}
		$this->set(compact('leaders'));
	}
	
}
?>