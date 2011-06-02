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
				'RequestStatus'
			)
		);
		
		if (!empty($this->data)) {
			$this->data = Set::filter($this->data);
			$this->paginate['conditions'] = $this->postConditions($this->data, 'LIKE');
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
				'RequestStatus'
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
				$this->Session->setFlash(__('Your request has been sent.', true), 'flash'.DS.'success');
			} else {
				$this->Session->setFlash(__('Your request could not be submitted. Please try again.', true), 'flash'.DS.'failure');
			}
		}
		$this->set('requestTypes', $this->Request->RequestType->find('list'));
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
			foreach ($selected as $requestId) {
				$this->Request->contain(array(
					'User',
					'RequestType',
					'RequestStatus'
				));
				$request = $this->Request->read(null, $requestId);
				$request = Set::merge($request, $this->data);
				
				if ($this->Request->save($this->data['Request'])) {
					$this->set('type', $request['RequestType']['name']);
					$this->set('status', $request['RequestStatus']['name']);
					$this->Notifier->notify(array(
						'to' => $request['User']['id'],
						'template' => 'CommunicationsRequests.edit_request'
					), 'notification');
					$this->Session->setFlash(__('The selected requests have been edited.', true), 'flash'.DS.'success');
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
			$this->Session->setFlash('Invalid id for Request', 'flash'.DS.'failure');
			$this->redirect(array('action'=>'index'));
		}
		$selected = $this->MultiSelect->getSelected($mskey);
		if (empty($selected)) {
			$selected = array($mskey);
		}
		foreach ($selected as $requestId) {
			$this->Request->delete($requestId);
			$this->Session->setFlash('Requests deleted', 'flash'.DS.'success');
		}
		$this->redirect(array('action' => 'index'));
	}
	
}