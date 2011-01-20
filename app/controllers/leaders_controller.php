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
	var $helpers = array('Formatting');
	
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
	
/**
 * A list of leaders
 */
	function index() {	
		$this->Leader->recursive = 1;
		$this->set('leaders', $this->paginate(array(
			'model' => $this->model,
			'model_id' => $this->modelId
			)
		));
		
		$this->set('model', $this->model);
		$this->set('modelId', $this->modelId);
	}
	
/**
 * Adds a leader
 *
 * @todo check if they already exists and if they're allowed to lead
 */
	function add() {
		if (!empty($this->data)) {
			$this->Leader->create();
			if ($this->Leader->save($this->data)) {				
				$model = $this->data['Leader']['model'];

				$this->Leader->User->contain(array('Profile'));
				$leader = $this->Leader->User->read(null, $this->data['Leader']['user_id']);
				$item = $this->Leader->{$model}->read(array('name'), $this->data['Leader']['model_id']);

				$itemType = $this->data['Leader']['model'];
				$itemName = $item[$model]['name'];
				$name = $leader['Profile']['name'];
				$type = $model == 'Involvement' ? 'leading' : 'managing';
				
				$this->set(compact('itemType','itemName','name','type'));
				
				// notify this user
				$this->Notifier->notify(array(
					'to' => $leader['User']['id'],
					'template' => 'leaders_add'
				), 'notification');
				
				// notify the managers as well
				$managers = $this->Leader->getManagers($this->data['Leader']['model'], $this->data['Leader']['model_id']);
				
				foreach ($managers as $manager) {
					$this->Notifier->notify(array(
						'to' => $manager,
						'template' => 'leaders_add'
					), 'notification');
				}
				
				$this->Session->setFlash('The leader has been added', 'flash'.DS.'success');
				//$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('The leader could not be added. Please, try again.', 'flash'.DS.'failure');
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

		if ($leaderCount <= 1) {
			$type = $this->model == 'Involvement' ? 'leader' : 'manager';
			$this->Session->setFlash('There needs to be at least one '.$type.'!', 'flash'.DS.'failure');
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
		$item = $this->Leader->{$this->model}->read(array('name'), $this->modelId);
		
		if ($this->Leader->delete($leaderId['Leader']['id'])) {
			$itemType = $this->model;
			$itemName = $item[$this->model]['name'];
			$name = $leader['Profile']['name'];
			$type = $this->model == 'Involvement' ? 'leading' : 'managing';
			
			$this->set(compact('itemType','itemName','name','type'));
			
			// notify this user
			$this->Notifier->notify(array(
					'to' => $leader['User']['id'],
					'template' => 'leaders_delete'
				), 'notification');
			
			// notify the managers as well
			$managers = $this->Leader->getManagers($this->model, $this->modelId);
			
			foreach ($managers as $manager) {
				if ($manager != $this->passedArgs['User']) {
					$this->Notifier->notify(array(
						'to' => $manager,
						'template' => 'leaders_delete'
					), 'notification');
				}
			}
		
			$this->Session->setFlash('Leader removed', 'flash'.DS.'success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('Leader was not removed', 'flash'.DS.'failure');
		$this->redirect(array('action' => 'index'));
	}
}
?>