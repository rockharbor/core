<?php
/**
 * Request Notifiers controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       communications_requests
 * @subpackage    communications_requests.controllers
 */

/**
 * RequestNotifiers Controller
 *
 * @package       communications_requests
 * @subpackage    communications_requests.controllers
 */
class RequestNotifiersController extends CommunicationsRequestsAppController {
	
/**
 * Extra helpers for this controller
 * 
 * @var array
 */
	var $helpers = array(
		'Formatting'
	);
	
/**
 * Extra components for this controller
 * 
 * @var array
 */
	var $components = array(
		'MultiSelect.MultiSelect'
	);
	
/**
 * List of all users to be notified for a certain request type
 */
	function index() {
		$requestType = $this->passedArgs['RequestType'];
		$requestNotifiers = $this->RequestNotifier->find('all', array(
			'conditions' => array(
				'request_type_id' => $requestType
			),
			'contain' => array(
				'User' => array(
					'Profile' => array(
						'fields' => array('name')
					)
				),
				'RequestType'
			)
		));
		$this->set(compact('requestNotifiers', 'requestType'));
	}
	
/**
 * Adds a user as a request notifier
 */
	function add($mskey = null) {
		$selected = $this->MultiSelect->getSelected($mskey);
		$requestTypeId = $this->passedArgs['RequestType'];
		
		$requestType = $this->RequestNotifier->RequestType->read(null, $requestTypeId);
		foreach ($selected as $userId) {
			$data = array(
				'user_id' => $userId,
				'request_type_id' => $requestTypeId
			);
			$this->RequestNotifier->create($data);
			if ($this->RequestNotifier->save()) {
				$this->set('name', $requestType['RequestType']['name']);
				$this->Notifier->notify(
					array(
						'to' => $userId,
						'template' => 'CommunicationsRequests.add_notifier'
					),
					'notification'
				);
				$this->Session->setFlash('The users will now be notified of a new '.$requestType['RequestType']['name'].' request.', 'flash'.DS.'success');
			}
		}
		$this->redirect(array('action' => 'index', 'RequestType' => $requestTypeId));
	}
	
/**
 * Deletes a request notifier
 * 
 * @param int $id The request notifier id
 */
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Notifier', 'flash'.DS.'failure');
			$this->redirect(array('action'=>'index', 'RequestType' => $this->passedArgs['RequestType']));
		}
		if ($this->RequestNotifier->delete($id)) {
			$this->Session->setFlash('Notifier deleted', 'flash'.DS.'success');
			$this->redirect(array('action'=>'index', 'RequestType' => $this->passedArgs['RequestType']));
		}
		$this->Session->setFlash('Notifier was not deleted', 'flash'.DS.'failure');
		$this->redirect(array('action' => 'index', 'RequestType' => $this->passedArgs['RequestType']));
	}
	
}