<?php
/**
 * Campus controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Campuses Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class CampusesController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Campuses';
	
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
				$this->Session->setFlash('The changes to this campus have been made.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('There were problems saving the changes.', 'flash'.DS.'failure');
			}
		}
		
		if (empty($this->data)) {
			$this->data = $this->Campus->read(null, $id);
		}
	}

/**
 * Toggles the `active` field for a Campus
 *
 * ### Requirements:
 * - At least 1 leader must be added
 *
 * @param boolean $active Whether to make the model inactive or active
 * @param boolean $recursive Whether to iterate through the model's relationships and mark them as $active
 */
	function toggle_activity($active = false, $recursive = false) {
		$id = $this->passedArgs['Campus'];

		if (!$id) {
			$this->Session->setFlash('Invalid id', 'flash'.DS.'failure');
			$this->redirect(array('action' => 'edit', $id));
		}

		// get involvement
		$this->Campus->contain(array('Leader'));
		$campus = $this->Campus->read(null, $id);
		if (empty($campus['Leader'])) {
			$this->Session->setFlash('Cannot activate until a manager is added', 'flash'.DS.'failure');
			$this->redirect($this->emptyPage);
			return;
		}

		$success = $this->Campus->toggleActivity($id, $active, $recursive);

		if ($success) {
			$this->Session->setFlash(
				'Successfully '.($active ? 'activated' : 'deactivated')
				.' Campus '.$id.' '
				.($recursive ? ' and all related items' : ''),
				'flash'.DS.'success'
			);
		} else {
			$this->Session->setFlash(
				'Failed to '.($active ? 'activate' : 'deactivate')
				.' Campus '.$id.' '
				.($recursive ? ' and all related items' : ''),
				'flash'.DS.'failure'
			);
		}
		$this->data = array();
		$this->redirect($this->emptyPage);
	}
	
/**
 * Deletes a campus
 *
 * @param integer $id The id of the campus to delete
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