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
 * Extra components for this controller
 * 
 * @var array
 */
	var $components = array(
		'FilterPagination' => array(
			'startEmpty' => false
		)
	);

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
					'private' => 1
				)
			);
		}
		
		$conditions = array(
			'Leader.model' => 'Involvement',
			'Leader.user_id' => $this->passedArgs['User'],
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
		
		$this->viewPath = 'involvement_leaders';
		$this->paginate = array(
			'conditions' => $conditions,
			'contain' => array(
				'Involvement' => array(
					'Ministry' => array(
						'ParentMinistry',
						'Campus'
					)
				)
			)
		);
		$leaders = $this->FilterPagination->paginate('Leader');
		$this->set('leaders', $leaders);
		$this->set('model', $this->model);
	}
	
}
?>