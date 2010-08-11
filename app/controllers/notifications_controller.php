<?php
/**
 * Notification controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Notifications Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class NotificationsController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Notifications';

/**
 * Extra components for this controller
 *
 * @var array
 */
	var $components = array('MultiSelect.MultiSelect');

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array('MultiSelect.MultiSelect');
	
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
 * Views a list of notifications
 *
 * @param string $typeFilter A quick filter for the NotificationType
 */
	function index($typeFilter = '') {
		if (isset($this->passedArgs['User'])) {
			$userId = $this->passedArgs['User'];
		} else {
			$userId = $this->activeUser['User']['id'];
		}
		
		$conditions['Notification.user_id'] = $userId;
		if (!empty($typeFilter) && in_array($typeFilter, array_keys($this->Notification->types))) {
			$conditions['Notification.type'] = $typeFilter;
		}
		
		$this->paginate = array(
			'conditions' => $conditions,
			'order' => array(
				'Notification.created DESC'
			)
		);
		$this->Notification->recursive = -1;
		$this->MultiSelect->saveSearch($this->paginate);
		$this->set('notifications', $this->paginate());
	}

/**
 * Marks a notification as `read`
 *
 * @param integer $id The notification id
 */
	function read($id = null) {
		if (!$id) {
			//$this->Session->setFlash('Could not mark notification as read', 'flash'.DS.'failure');
			$this->redirect(array('action'=>'index'));
		}
		
		// check to see if this is a MultiSelect
		if ($this->MultiSelect->check($id)) {
			
			$search = $this->MultiSelect->getSearch($id);
			$selected = $this->MultiSelect->getSelected($id);
			
			$search['conditions']['Notification.id'] = $selected;
			$results = $this->Notification->find('all', $search);
			$ids = $selected; //Set::extract('/Notification/id', $results);
		} else {
			$ids = array($id);
		}
		
		foreach ($ids as $id) {
			$this->Notification->id = $id;
			if ($this->Notification->ownedBy($this->activeUser['User']['id'])) {
				if ($this->Notification->saveField('read', true)) {
					//$this->Session->setFlash('Notification marked as read', 'flash'.DS.'success');
				} else {
					//$this->Session->setFlash('Could not mark notification as read', 'flash'.DS.'failure');
				}
			} else {
				//$this->Session->setFlash('Could not mark notification as read', 'flash'.DS.'failure');
			}
		}
	
		$this->redirect(array('action' => 'index'));
	}

/**
 * Marks a notification as `deleted`
 *
 * @param integer $id The notification id
 */
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Could not delete notification', 'flash'.DS.'failure');
			$this->redirect(array('action'=>'index'));
		}
		
		// check to see if this is a MultiSelect
		if ($this->MultiSelect->check($id)) {
			$search = $this->MultiSelect->getSearch($id);
			$selected = $this->MultiSelect->getSelected($id);
						
			$search['conditions']['Notification.id'] = $selected;			
			$results = $this->Notification->find('all', $search);
			$ids = $selected; // Set::extract('/Notification/id', $results);
		} else {
			$ids = array($id);
		}

		foreach ($ids as $id) {
			$this->Notification->id = $id;
			if ($this->Notification->ownedBy($this->activeUser['User']['id'])) {
				if ($this->Notification->delete($id)) {
					$this->Session->setFlash('Notification deleted', 'flash'.DS.'success');
				} else {
					$this->Session->setFlash('Could not delete notification', 'flash'.DS.'failure');
				}
			} else {
				$this->Session->setFlash('Could not delete notification', 'flash'.DS.'failure');
			}
		}
		
		$this->redirect(array('action' => 'index'));
	}
}
?>