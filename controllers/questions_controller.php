<?php
class QuestionsController extends AppController {

	var $name = 'Questions';

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
 * Shows a list of questions
 */ 
	function index() {
		$this->Question->recursive = 0;
		$this->set('questions', $this->Question->find('all', array(
			'conditions' => array(
				'involvement_id' => $this->passedArgs['Involvement']
			)
		)));
		
		$this->set('involvementId', $this->passedArgs['Involvement']);
	}

/**
 * Adds a question
 */ 
	function add() {
		if (!empty($this->data)) {
			$this->Question->create();
			if ($this->Question->save($this->data)) {
				$this->Session->setFlash(__('The question has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The question could not be saved. Please, try again.', true));
			}
		}
		
		$this->set('involvementId', $this->passedArgs['Involvement']);
	}

/**
 * Edits a question
 */ 
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid question', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Question->save($this->data)) {
				$this->Session->setFlash(__('The question has been saved', true));
			} else {
				$this->Session->setFlash(__('The question could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Question->read(null, $id);
		}
	}

/**
 * Moves a question order
 *
 * #### Directions:
 * - `up` Moves question up one
 * - `down` Moves question down  one
 *
 * @param integer $id Question id
 * @param string $direction The direction to move the question
 */
	function move($id = null, $direction = null) {
		if (!$id || !$direction) {
			$this->Session->setFlash('Invalid');
			$this->redirect(array('action' => 'index'));
		}
		
		$this->Question->{'move'.$direction}();
		
		$this->redirect(array('action' => 'index'));
	}
	
/**
 * Deletes a question
 */ 
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for question', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Question->delete($id)) {
			$this->Session->setFlash(__('Question deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Question was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>