<?php
class MinistriesController extends AppController {

	var $name = 'Ministries';
	
	var $helpers = array('Formatting', 'Tree');

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
 * Shows a list of ministries
 */ 
	function index() {
		$this->Ministry->recursive = 0;
		$this->set('ministries', $this->paginate());
		
		$this->set('ministryMenu', $this->Ministry->find('all', array('order' => 'Ministry.lft ASC')));
	}

/**
 * Ministry details
 */ 
	function view() {
		$id = $this->passedArgs['Ministry'];
		
		if (!$id) {
			$this->Session->setFlash(__('Invalid ministry', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$this->set('ministryMenu', $this->Ministry->find('all', array('order' => 'Ministry.lft ASC')));
		
		$this->set('ministry', $this->Ministry->find('first', array(
			'conditions' => array(
				'Ministry.id' => $id
			),
			'contain' => array(
				'Involvement' => array(
					'InvolvementType'
				),
				'Campus',
				'Group'
			)
		)));
	}

/**
 * Adds a ministry
 */ 
	function add() {
		$this->Ministry->Behaviors->detach('Confirm');
		
		if (!empty($this->data)) {
			$this->Ministry->create();
			if ($this->Ministry->save($this->data)) {
				$this->Session->setFlash(__('The ministry has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The ministry could not be saved. Please, try again.', true));
			}
		}
		
		$this->set('groups', $this->Ministry->Group->find('list'));
		$this->set('campuses', $this->Ministry->Campus->find('list'));
		$this->set('ministries', $this->Ministry->find('list', array(
			'conditions' => array(
				'Ministry.parent_id' => null
			)
		)));
	}

/**
 * Edits a ministry
 */ 
	function edit() {
		$id = $this->passedArgs['Ministry'];
	
		$this->Ministry->Behaviors->attach('Confirm');
	
		if (!$id) {
			$this->Session->setFlash('Invalid ministry');
			$this->redirect(array('action' => 'index'));
		}		
		
		$this->Ministry->id = $id;
		$revision = $this->Ministry->revision($id);
		
		if (!empty($this->data)) {
			if (!$revision) {
				if ($this->Ministry->save($this->data)) {
					$this->Session->setFlash('The changes to this ministry are pending.', 'flash_success');
					
					$this->Notifier->notify($this->CORE['settings']['ministry_content_edit_user'], 'ministries_edit');
					$this->_sendEmail(array(
						'to' => $this->CORE['settings']['ministry_content_edit_user'],
						'subject' => 'Ministry content change',
						'template' => 'ministries_edit'
					));
				} else {
					$this->Session->setFlash('There were problems saving the changes.', 'flash_failure');
				}
				
				$revision = $this->Ministry->revision($id);
			} else {
				$this->Session->setFlash('There\'s already a pending revision for this ministry.', 'flash_failure');
			}
		}
		
		$this->data = $this->Ministry->read(null, $id);		
		
		$this->set('groups', $this->Ministry->Group->find('list'));
		$this->set('campuses', $this->Ministry->Campus->find('list'));
		$this->set('ministries', $this->Ministry->find('list', array(
			'conditions' => array(
				'Ministry.parent_id' => null,
				'not' => array(
					'Ministry.id' => $id
				)
			)
		)));
		
		
		$this->set('revision', $revision);
	}
	
/**
 * Displays ministry revision history (up to 1 change)
 */ 	
	function history($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid ministry', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$this->set('ministry', $this->Ministry->read(null, $id));
		
		// get the most recent change (not quite using revisions as defined, but close)
		$this->set('groups', $this->Ministry->Group->find('list'));
		$this->set('campuses', $this->Ministry->Campus->find('list'));
		$this->set('parents', $this->Ministry->find('list'));
		$this->set('revision', $this->Ministry->revision($id));
	}

/**
 * Revises a ministry (confirm or deny revision)
 */ 	
	function revise($id, $confirm = false) {
		if ($confirm) {
			$success = $this->Ministry->confirmRevision($id);
		} else {
			$success = $this->Ministry->denyRevision($id);
		}
		
		if ($success) {
			$this->Session->setFlash('Action taken');
		} else {
			$this->Session->setFlash('Error');
		}		
		
		$this->redirect(array('action' => 'history', 'Ministry' => $id));
	}

/**
 * Deletes a ministry
 */ 
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for ministry', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Ministry->delete($id)) {
			$this->Session->setFlash(__('Ministry deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Ministry was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>