<?php
/**
 * Leader controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Leaders Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class LeadersController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Leaders';

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array('Formatting', 'MultiSelect.MultiSelect');

/**
 * Extra components for this controller
 *
 * @var array
 */
	var $components = array(
		'MultiSelect.MultiSelect',
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
		$this->_editSelf('dashboard');
	}
	
/**
 * A list of leaders
 */
	function index() {	
		$this->paginate = array(
			'conditions' => array(
				'model' => $this->model,
				'model_id' => $this->modelId
			),
			'contain' => array(
				'User' => array(
					'Profile'
				)
			)
		);
		$this->set('leaders', $this->paginate());		
		$this->set('model', $this->model);
		$this->set('modelId', $this->modelId);
	}

/**
 * A list of Involvements, Ministries or Campuses a user is a leader for
 */
	function dashboard() {
		
	}
	
/**
 * Adds a leader
 *
 * @param string $mskey The MultiSelect token
 * @todo check if they already exists and if they're allowed to lead
 */
	function add($mskey) {
		$userIds = $this->MultiSelect->getSelected($mskey);
		
		$model = $this->passedArgs['model'];
		$model_id = $this->passedArgs[$this->passedArgs['model']];
		$managers = $this->Leader->getManagers($model, $model_id);
		$item = $this->Leader->{$model}->read(array('name'), $model_id);
				
		foreach ($userIds as $userId) {
			$data = array(
				'Leader' => array(
					'model' => $model,
					'model_id' => $model_id,
					'user_id' => $userId,
				)
			);
			
			$this->Leader->create();
			if ($this->Leader->save($data)) {
				$this->Leader->User->contain(array('Profile'));
				$leader = $this->Leader->User->read(null, $data['Leader']['user_id']);
				$itemType = $data['Leader']['model'] == 'Involvement' ? 'Involvement Opportunities' : 'Ministry';
				$item = $this->Leader->{$data['Leader']['model']}->read(null, $model_id);

				$this->set(compact('model', 'leader', 'item', 'itemType'));

				// notify this user
				$this->Notifier->notify(array(
					'to' => $userId,
					'template' => 'leaders_add'
				));

				// notify the managers as well
				foreach ($managers as $manager) {
					$this->Notifier->notify(array(
						'to' => $manager,
						'template' => 'leaders_add'
					));
				}

				$this->Session->setFlash($leader['Profile']['name'].' is now a leader of '.$item[$model]['name'].'.', 'flash'.DS.'success');
				//$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Unable to process this request. Please try again.', 'flash'.DS.'failure');
			}
		}
		$this->redirect(array(	
			'action' => 'index')
		);
	}
	
/**
 * Deletes a leader
 *
 * ### Passed args:
 * - The model id where the key is the name of the model, i.e., Involvement:1
 * - `User` The user to remove
 *
 * @todo check if leader exists
 */
	function delete() {
		if (!$this->model || !$this->modelId || !isset($this->passedArgs['User'])) {
			$this->Session->setFlash('Invalid id for leader', 'flash'.DS.'failure');
			$this->redirect(array('action'=>'index'));
		}

		$leaderCount = $this->Leader->find('count', array(
			'conditions' => array(
				'model' => $this->model,
				'model_id' => $this->modelId
			)
		));

		$item = $this->Leader->{$this->model}->read(array('name'), $this->modelId);

		if ($leaderCount <= 1) {
			$this->Session->setFlash($item[$this->model]['name'].' cannot be without a leader.', 'flash'.DS.'failure');
			$this->redirect(array('action'=>'index'));
		}

		$leaderId = $this->Leader->find('first', array(
			'fields' => array(
				'id'
			),
			'conditions' => array(
				'model' => $this->model,
				'model_id' => $this->modelId,
				'user_id' => $this->passedArgs['User']
			)
		));
		$this->Leader->User->contain(array('Profile'));
		$leader = $this->Leader->User->read(null, $this->passedArgs['User']);
		
		if ($this->Leader->delete($leaderId['Leader']['id'])) {
			$this->Leader->User->contain(array('Profile'));
			$leader = $this->Leader->User->read(null, $this->passedArgs['User']);
			$itemType = $this->model == 'Involvement' ? 'Involvement Opportunities' : 'Ministry';
			$item = $this->Leader->{$this->model}->read(null, $this->modelId);
			$model = $this->model;

			$this->set(compact('model', 'leader', 'item', 'itemType'));
			
			// notify this user
			$this->Notifier->notify(array(
					'to' => $leader['User']['id'],
					'template' => 'leaders_delete'
				));
			
			// notify the managers as well
			$managers = $this->Leader->getManagers($this->model, $this->modelId);
			
			foreach ($managers as $manager) {
				if ($manager != $this->passedArgs['User']) {
					$this->Notifier->notify(array(
						'to' => $manager,
						'template' => 'leaders_delete'
					));
				}
			}
		
			$this->Session->setFlash($leader['Profile']['name'].' has been removed from leading '.$item[$model]['name'].'.', 'flash'.DS.'success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('Unable to process request. Please try again.', 'flash'.DS.'failure');
		$this->redirect(array('action' => 'index'));
	}
}
?>