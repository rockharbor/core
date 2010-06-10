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
 */
	function add() {
		if (!empty($this->data)) {
			$this->Leader->create();
			if ($this->Leader->save($this->data)) {
				$this->Leader->bindModel(array(
					'belongsTo' => array(
						$this->data['Leader']['model'] => array(
							'foreignKey' => 'model_id'
						)
					)
				));
				
				$parentModel = $this->data['Leader']['model'] == 'Involvement' ? 'Ministry' : 'Campus';
				
				if ($this->data['Leader']['model'] == 'Campus') {
					$this->Leader->contain(array(
						'User' => array(
							'Profile'
						),
						$this->data['Leader']['model']
					));
				} else {
					$this->Leader->contain(array(
						'User' => array(
							'Profile'
						),
						$this->data['Leader']['model'] => array(
							$parentModel
						)
					));
				}
				
				$leader = $this->Leader->find('first', array(
					'conditions' => array(
						'model' => $this->data['Leader']['model'],
						'model_id' => $this->data['Leader']['model_id'],
						'user_id' => $this->data['Leader']['user_id']
					)
				));
				
				$itemType = $this->data['Leader']['model'];
				$itemName = $leader[$this->data['Leader']['model']]['name'];
				$name = $leader['User']['Profile']['name'];
				$type = $this->data['Leader']['model'] == 'Involvement' ? 'leading' : 'managing';
				
				$this->set(compact('itemType','itemName','name','type'));
				
				// notify this user
				$this->Notifier->notify($leader['User']['id'], 'leaders_add');
				
				// notify the managers as well
				$managers = $this->Leader->getManagers($this->data['Leader']['model'], $this->data['Leader']['model_id']);
				
				foreach ($managers as $manager) {
					$this->Notifier->notify($manager['User']['id'], 'leaders_add');
				}
				
				$this->Session->setFlash('The leader has been added', 'flash_success');
				//$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('The leader could not be added. Please, try again.', 'flash_failure');
			}
		}
		
		$this->redirect(array(	
			'action' => 'index')
		);
	}
	
/**
 * Deletes a leader
 */
	function delete() {
		if (!$this->model || !$this->modelId || !isset($this->passedArgs['User'])) {
			$this->Session->setFlash('Invalid id for leader', 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}
		
		$this->Leader->bindModel(array(
			'belongsTo' => array(
				$this->model => array(
					'foreignKey' => 'model_id'
				)
			)
		));
		
		$parentModel = $this->model == 'Involvement' ? 'Ministry' : 'Campus';
		
		if ($this->model == 'Campus') {
			$this->Leader->contain(array(
				'User' => array(
					'Profile'
				),
				$this->model
			));
		} else {
			$this->Leader->contain(array(
				'User' => array(
					'Profile'
				),
				$this->model => array(
					$parentModel
				)
			));
		}
		
		$leader = $this->Leader->find('first', array(
			'conditions' => array(
				'model' => $this->model,
				'model_id' => $this->modelId,
				'user_id' => $this->passedArgs['User']
			)
		));
		
		if ($this->Leader->delete($leader['Leader']['id'])) {			
			$itemType = $this->model;
			$itemName = $leader[$this->model]['name'];
			$name = $leader['User']['Profile']['name'];
			$type = $this->model == 'Involvement' ? 'leading' : 'managing';
			
			$this->set(compact('itemType','itemName','name','type'));
			
			// notify this user
			$this->Notifier->notify($leader['User']['id'], 'leaders_delete');
			
			// notify the managers as well
			$managers = $this->Leader->getManagers($this->model, $this->modelId);
			
			foreach ($managers as $manager) {
				if ($manager['User']['id'] != $this->passedArgs['User']) {
					$this->Notifier->notify($manager['User']['id'], 'leaders_delete');
				}
			}
		
			$this->Session->setFlash('Leader removed', 'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('Leader was not removed', 'flash_failure');
		$this->redirect(array('action' => 'index'));
	}
}
?>