<?php
class NotificationsController extends AppController {

	var $name = 'Notifications';
	
	var $components = array('MultiSelect');
	
	var $helpers = array('Template', 'MultiSelect');
	
/**
 * Model::beforeFilter() callback
 *
 * Sets permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * Views a list of notifications
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
			//$this->Session->setFlash('Could not mark notification as read', 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}
		
		// check to see if this is a MultiSelect
		if ($this->MultiSelect->check($id)) {
			$search = $this->MultiSelect->getSearch($id);
			$selected = $this->MultiSelect->getSelected($id);
			
			if ($selected == 'all' || !empty($selected)) {
				if ($selected != 'all') {
					$search['conditions']['Notification.id'] = $selected;
				} 
				$results = $this->Notification->find('all', $search);
				$ids = Set::extract('/Notification/id', $results);
			} else {
				$ids = array();
			}
		} else {
			$ids = array($id);
		}
		
		foreach ($ids as $id) {
			$this->Notification->id = $id;
			if ($this->Notification->ownedBy($this->activeUser['User']['id'])) {
				if ($this->Notification->saveField('read', true)) {
					//$this->Session->setFlash('Notification marked as read', 'flash_success');
				} else {
					//$this->Session->setFlash('Could not mark notification as read', 'flash_failure');
				}
			} else {
				//$this->Session->setFlash('Could not mark notification as read', 'flash_failure');
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
			$this->Session->setFlash('Could not delete notification', 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}
		
		// check to see if this is a MultiSelect
		if ($this->MultiSelect->check($id)) {
			$search = $this->MultiSelect->getSearch($id);
			$selected = $this->MultiSelect->getSelected($id);
			
			if ($selected == 'all' || !empty($selected)) {
				if ($selected != 'all') {
					$search['conditions']['Notification.id'] = $selected;
				} 
				$results = $this->Notification->find('all', $search);
				$ids = Set::extract('/Notification/id', $results);
			} else {
				$ids = array();
			}
		} else {
			$ids = array($id);
		}
		
		foreach ($ids as $id) {
			$this->Notification->id = $id;
			if ($this->Notification->ownedBy($this->activeUser['User']['id'])) {
				if ($this->Notification->delete($id)) {
					$this->Session->setFlash('Notification deleted', 'flash_success');
				} else {
					$this->Session->setFlash('Could not delete notification', 'flash_failure');
				}
			} else {
				$this->Session->setFlash('Could not delete notification', 'flash_failure');
			}
		}
		
		$this->redirect(array('action' => 'index'));
	}
}
?>