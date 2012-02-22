<?php
/**
 * Requests controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       communications_requests
 * @subpackage    communications_requests.controllers
 */

/**
 * Requests Controller
 *
 * @package       communications_requests
 * @subpackage    communications_requests.controllers
 */
class RequestsController extends CommunicationsRequestsAppController {
	
/**
 * Extra helpers for this controller
 * 
 * @var array
 */
	var $helpers = array(
		'Formatting',
		'MultiSelect.MultiSelect'
	);
	
/**
 * Additional components for this controller
 * 
 * @var array
 */
	var $components = array(
		'FilterPagination' => array(
			'startEmpty' => false
		),
		'MultiSelect.MultiSelect'
	);
	
/**
 * Shows a list of requests
 */
	function index() {
		$this->paginate = array(
			'link' => array(
				'User' => array(
					'fields' => array(
						'id'
					),
					'Profile' => array(
						'fields' => array(
							'name'
						)
					)
				),
				'RequestType',
				'RequestStatus',
				'Involvement'
			)
		);
		
		if (!empty($this->data)) {
			$this->data = Set::filter($this->data);
			$filter = array('Request' => $this->data['Filter']);
			$this->paginate['conditions'] = $this->postConditions($filter, 'LIKE');
		}
		
		$this->set('requestStatuses', $this->Request->RequestStatus->find('list'));
		$this->set('requestTypes', $this->Request->RequestType->find('list'));
		$this->set('requests', $this->FilterPagination->paginate());
	}

/**
 * Shows a history of requests for a user
 */
	function history() {
		$this->paginate = array(
			'contain' => array(
				'RequestType',
				'RequestStatus',
				'Involvement'
			),
			'conditions' => array(
				'user_id' => $this->activeUser['User']['id']
			)
		);
		$this->set('requests', $this->paginate());
	}
	
/**
 * Adds a request
 */
	function add() {
		if (!empty($this->data)) {
			$this->Request->create();
			$this->data['Request']['request_status_id'] = 2;
			if ($this->Request->save($this->data)) {
				$this->Request->contain(array(
					'RequestType' => array(
						'RequestNotifier'
					)
				));
				$request = $this->Request->read();
				$users = Set::extract('/RequestType/RequestNotifier/user_id', $request);
				$this->set('type', $request['RequestType']['name']);
				foreach ($users as $userId) {
					$this->Notifier->notify(array(
						'to' => $userId,
						'template' => 'CommunicationsRequests.add_request'
					), 'notification');
				}
				$this->Session->setFlash(__('Your communication request has been received.', true), 'flash'.DS.'success');
			} else {
				$this->Session->setFlash(__('Unable to send your request. Please try again.', true), 'flash'.DS.'failure');
			}
		}
		$requestTypes = $this->Request->RequestType->find('all');
		$this->set('requestTypeDescriptions', Set::combine($requestTypes, '{n}.RequestType.id', '{n}.RequestType.description'));
		$this->set('requestTypes', Set::combine($requestTypes, '{n}.RequestType.id', '{n}.RequestType.name'));
		$leading = $this->Request->Involvement->Leader->find('all', array(
			'fields' => array(
				'model_id'
			),
			'conditions' => array(
				'Leader.user_id' => $this->activeUser['User']['id'],
				'Leader.model' => 'Involvement'
			)
		));
		$involvements = $this->Request->Involvement->find('list', array(
			'conditions' => array(
				'id' => Set::extract('/Leader/model_id', $leading)
			)
		));
		
		$this->set(compact('involvements'));
	}

/**
 * Edits one or a multiselect key selection of requests 
 * 
 * @param mixed $status Either an MS key or a single request to edit
 */
	function edit($mskey = null) {
		if (!empty($this->data)) {
			$selected = $this->MultiSelect->getSelected($mskey);
			if (empty($selected)) {
				$selected = array($mskey);
			}
			$this->Request->contain(array(
				'User',
				'RequestType',
				'RequestStatus'
			));
			foreach ($selected as $requestId) {
				$this->Request->create();
				$this->Request->id = $requestId;
				if ($this->Request->save($this->data['Request'])) {
					$request = $this->Request->read(null, $requestId);
					$this->set('type', $request['RequestType']['name']);
					$this->set('status', $request['RequestStatus']['name']);
					$this->Notifier->notify(array(
						'to' => $request['User']['id'],
						'template' => 'CommunicationsRequests.edit_request'
					), 'notification');
					$this->Session->setFlash(__('The selected requests have been updated.', true), 'flash'.DS.'success');
				}
			}
		}
		$this->set('requestStatuses', $this->Request->RequestStatus->find('list'));
	}
	
/**
 * Deletes a request
 * 
 * @param mixed $mskey Either an MS key or a single request to delete
 */
	function delete($mskey = null) {
		if (!$mskey) {
			//404
			$this->Session->setFlash('Invalid id for Request', 'flash'.DS.'failure');
			$this->redirect(array('action'=>'index'));
		}
		$selected = $this->MultiSelect->getSelected($mskey);
		if (empty($selected)) {
			$selected = array($mskey);
		}
		foreach ($selected as $requestId) {
			$this->Request->delete($requestId);
			$this->Session->setFlash('The selected requests have been deleted.', 'flash'.DS.'success');
		}
		$this->redirect(array('action' => 'index'));
	}
	
}