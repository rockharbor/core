<?php
/**
 * Question controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Questions Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class QuestionsController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	public $name = 'Questions';

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 */
	public function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * Shows a list of questions
 */
	public function index() {
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
	public function add() {
		if (!empty($this->data)) {
			$this->Question->create();
			if ($this->Question->save($this->data)) {
				$this->Session->setFlash('This question has been created.', 'flash'.DS.'success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Unable to create this question. Please try again.', 'flash'.DS.'failure');
			}
		}

		$this->set('involvementId', $this->passedArgs['Involvement']);
	}

/**
 * Edits a question
 *
 * @param integer $id The id of the question to edit
 * @todo Add involvement named arg to restrict to leaders, etc.
 */
	public function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->cakeError('error404');
		}
		if (!empty($this->data)) {
			if ($this->Question->save($this->data)) {
				$this->Session->setFlash('This question has been saved.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Unable to save this question. Please try again.', 'flash'.DS.'failure');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Question->read(null, $id);
		}
	}

/**
 * Moves a question order
 *
 * ### Directions:
 * - `up` Moves question up one
 * - `down` Moves question down  one
 *
 * @param integer $id Question id
 * @param string $direction The direction to move the question
 * @todo Add involvement named arg to restrict to leaders, etc.
 */
	public function move($id = null, $direction = null) {
		if (!$id || !$direction) {
			$this->cakeError('error404');
		}

		$success = $this->Question->{'move'.$direction}($id);

		$question = $this->Question->read(null, $id);

		if ($success) {
			$this->Session->setFlash('Moved question '.$direction.'.', 'flash'.DS.'success');
		} else {
			$this->Session->setFlash('Could not move question '.$direction.'. Please try again.', 'flash'.DS.'failure');
		}
		$this->redirect(array('action' => 'index', 'Involvement' => $question['Question']['involvement_id']));
	}

/**
 * Deletes a question
 *
 * @param integer $id The id of the question to delete
 * @todo Add involvement named arg to restrict to leaders, etc.
 */
	public function delete($id = null) {
		if (!$id) {
			$this->cakeError('error404');
		}
		if ($this->Question->delete($id)) {
			$this->Session->setFlash('This question has been deleted.', 'flash'.DS.'success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('Unable to delete this question. Please try again.', 'flash'.DS.'failure');
		$this->redirect(array('action' => 'index'));
	}
}
