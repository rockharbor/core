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

/**
 * Shows a leader dashboard
 *
 * @see LeadersController::dashboard()
 */
	function dashboard() {
		if ($this->Session->check('FilterPagination.data') && empty($this->data)) {
			$this->data = $this->Session->read('FilterPagination.data');
		}
		if (empty($this->data)) {
			$this->data = array(
				'Filter' => array(
					'previous' => 0,
					'inactive' => 0,
					'private' => 1,
					'affiliated' => 0
				)
			);
		}
		
		$conditions = array(
			'Involvement.active' => true,
			'Involvement.private' => false
		);
		if (!$this->data['Filter']['previous']) {
			$Involvement = ClassRegistry::init('Involvement');
			$db = $Involvement->getDataSource();
			$conditions[] = $db->expression('('.$Involvement->getVirtualField('previous').') = 0');
		}
		if ($this->data['Filter']['inactive']) {
			$conditions['Involvement.active'] = array(1, 0);
		}
		if ($this->data['Filter']['private']) {
			$conditions['Involvement.private'] = array(1, 0);
		}
		$leaders = $this->Leader->find('all', array(
			'fields' => array(
				'model_id'
			),
			'conditions' => array(
				'Leader.model' => 'Involvement',
				'Leader.user_id' => $this->passedArgs['User'],
			)
		));
		
		$inherited = array();
		if ($this->data['Filter']['affiliated']) {
			$ministries = $this->Leader->Ministry->getLeading($this->passedArgs['User'], true);
			$conditions['or']['Involvement.ministry_id'] = $ministries;
			$conditions['or']['Involvement.id'] = Set::extract('/Leader/model_id', $leaders);
		} else {
			$conditions['Involvement.id'] = Set::extract('/Leader/model_id', $leaders);
		}
		
		
		$this->viewPath = 'involvement_leaders';
		$this->paginate = array(
			'conditions' => $conditions,
			'contain' => array(
				'Ministry' => array(
					'ParentMinistry',
					'Campus'
				)
			)
		);
		$this->MultiSelect->saveSearch($this->paginate);
		// paginate needs to be on Involvement model for MultiSelect to work
		$involvements = $this->FilterPagination->paginate('Involvement');
		$this->set('involvements', $involvements);
		$this->set('model', $this->model);
	}
	
}
