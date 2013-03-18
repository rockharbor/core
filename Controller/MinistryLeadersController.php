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
	public $model = 'Ministry';

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->modelId = isset($this->passedArgs[$this->model]) ? $this->passedArgs[$this->model] : null;
	}

/**
 * Shows a manager dashboard
 *
 * @see LeadersController::dashboard()
 */
	public function dashboard() {
		$default = array(
			'Filter' => array(
				'inactive' => 0,
				'private' => 1,
				'affiliated' => 0
			)
		);
		$this->data = Set::merge($default, $this->data);

		$leaders = $this->Leader->find('all', array(
			'fields' => array(
				'model_id'
			),
			'conditions' => array(
				'Leader.model' => 'Ministry',
				'Leader.user_id' => $this->passedArgs['User'],
			)
		));

		$conditions = array(
			'Ministry.active' => 1,
			'Ministry.private' => 0
		);
		if ($this->data['Filter']['private']) {
			$conditions['Ministry.private'] = array(0, 1);
		}
		if ($this->data['Filter']['inactive']) {
			$conditions['Ministry.active'] = array(0, 1);
		}
		if ($this->data['Filter']['affiliated']) {
			$conditions['or']['Ministry.parent_id'] = Set::extract('/Leader/model_id', $leaders);
			$conditions['or']['Ministry.id'] = Set::extract('/Leader/model_id', $leaders);
		} else {
			$conditions['Ministry.id'] = Set::extract('/Leader/model_id', $leaders);
		}

		$this->viewPath = 'ministry_leaders';
		$this->paginate = array(
			'conditions' => $conditions,
			'contain' => array(
				'Role',
				'ParentMinistry',
				'Campus'
			)
		);
		$ministries = $this->FilterPagination->paginate('Ministry');
		$this->set('ministries', $ministries);
		$this->set('model', $this->model);
	}

}
