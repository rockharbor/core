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
		$this->_editSelf('quick', 'index');
		parent::beforeFilter();
	}

/**
 * Gets a list of notifications (specifically for the menu)
 */
	function quick() {
		// get alerts
		$Alert = ClassRegistry::init('Alert');
		$unread = $Alert->getUnreadAlerts($this->activeUser['User']['id'], $this->activeUser['Group']['id'], false);
		$alerts = $Alert->find('all', array(
			'conditions' => array(
				'Alert.id' => $unread
			),
			'order' => 'Alert.created DESC',
			'limit' => 5
		));
		$this->set('alerts', $alerts);

		// get notifications
		$this->set('new', $this->Notification->find('count', array(
			'conditions' => array(
				'Notification.user_id' => $this->Auth->user('id'),
				'Notification.read' => false
			)
		)));
		$this->paginate = array(
			'conditions' => array(
				'Notification.user_id' => $this->Auth->user('id')
			),
			'order' => 'Notification.created DESC',
			'limit' => 10
		);
		$this->set('notifications', $this->paginate());
	}

/**
 * Views a list of notifications
 *
 * @param string $typeFilter A quick filter for the NotificationType
 */
	function index($typeFilter = '') {
		$conditions['Notification.user_id'] = $this->Auth->user('id');
		if (!empty($typeFilter) && in_array($typeFilter, array_keys($this->Notification->types))) {
			$conditions['Notification.type'] = $typeFilter;
		}
		
		$this->paginate = array(
			'conditions' => $conditions,
			'order' => array(
				'Notification.created DESC'
			)
		);
		$this->MultiSelect->saveSearch($this->paginate);
		$this->set('notifications', $this->paginate());
	}

/**
 * Marks a notification as `read` or `unread`
 *
 * @param integer $id The notification id
 * @param boolean $read 1 for `read`, 0 for `unread`
 */
	function read($id = null, $read = 1) {
		if (!$id) {
			//404
			$this->redirect(array('action'=>'index'));
		}
		
		// check to see if this is a MultiSelect
		if ($this->MultiSelect->check($id)) {			
			$search = $this->MultiSelect->getSearch($id);
			$selected = $this->MultiSelect->getSelected($id);
			
			$search['conditions']['Notification.id'] = $selected;
			$results = $this->Notification->find('all', $search);
			$ids = $selected;
		} else {
			$ids = array($id);
		}
		
		foreach ($ids as $id) {
			$this->Notification->id = $id;
			if ($this->Notification->ownedBy($this->activeUser['User']['id'])) {
				$this->Notification->saveField('read', $read == 1);
			}
		}

		if ($read) {
			$this->Session->setFlash('Notifications have been marked as read.', 'flash'.DS.'success');
		} else {
			$this->Session->setFlash('Notifications have been marked as unread.', 'flash'.DS.'success');
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
			//404
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
				$this->Notification->delete($id);
			}
		}

		$this->Session->setFlash('Notifications have been deleted.', 'flash'.DS.'success');
		
		$this->redirect(array('action' => 'index'));
	}
}
?>