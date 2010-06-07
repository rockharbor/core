<?php

class CampusesController extends AppController {

	var $name = 'Campuses';
	
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
 * Shows a list of campuses
 */ 
	function index() {
		$this->Campus->recursive = 0;
		$this->set('campuses', $this->paginate());
	}

/**
 * Campus details
 */ 
	function view() {
		$id = $this->passedArgs['Campus'];
		
		if (!$id) {
			$this->Session->setFlash(__('Invalid campus', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$this->set('campus', $this->Campus->read(null, $id));
	}

/**
 * Adds a campus
 */ 
	function add() {		
		if (!empty($this->data)) {
			$this->Campus->create();
			if ($this->Campus->save($this->data)) {
				$this->Session->setFlash(__('The campus has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The campus could not be saved. Please, try again.', true));
			}
		}
	}

/**
 * Edits a campus
 */ 
	function edit() {
		$id = $this->passedArgs['Campus'];
	
		if (!$id) {
			$this->Session->setFlash('Invalid campus');
			$this->redirect(array('action' => 'index'));
		}		
		
		$this->Campus->id = $id;
		
		if (!empty($this->data)) {
			if ($this->Campus->save($this->data)) {
				$this->Session->setFlash('The changes to this campus have been made.', 'flash_success');
			} else {
				$this->Session->setFlash('There were problems saving the changes.', 'flash_failure');
			}
		}
		
		if (empty($this->data)) {
			$this->data = $this->Campus->read(null, $id);
		}
	}
	
	
/**
 * Deletes a campus
 */ 
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for campus', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Campus->delete($id)) {
			$this->Session->setFlash(__('Campus deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Campus was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}

}
?>