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
		$this->Campus->Behaviors->disable('Confirm');

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

		// if they can confirm a revision, there's no need to go through the confirmation process
		if ($this->isAuthorized('campuses/revise')) {
			$this->Campus->Behaviors->disable('Confirm');
		}

		$this->Campus->id = $id;
		$revision = $this->Campus->revision($id);
		
		if (!empty($this->data)) {
			if (!$revision) {
				if ($this->Campus->save($this->data)) {
					$this->Session->setFlash('The changes to this campus are pending.', 'flash'.DS.'success');
					
					$this->Notifier->notify(array(
						'to' => Core::read('notifications.campus_content'),
						'template' => 'campuses_edit',
						'subject' => 'Campus content change'
					));
				} else {
					$this->Campus->setFlash('There were problems saving the changes.', 'flash'.DS.'failure');
				}
				
				$revision = $this->Campus->revision($id);
			} else {
				$this->Session->setFlash('There\'s already a pending revision for this campus.', 'flash'.DS.'failure');
			}
		}
		
		if (empty($this->data)) {
			$this->data = $this->Campus->read(null, $id);
		}

		$this->set('revision', $revision);
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

		$this->Campus->Behaviors->disable('Confirm');
		$success = $this->Campus->toggleActivity($id, $active, $recursive);
		$this->Campus->Behaviors->enable('Confirm');

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
 * Displays campus revision history (up to 1 change)
 *
 * @param integer $id The id of the campus
 */
	function history() {
		$id = $this->passedArgs['Campus'];

		if (!$id) {
			$this->Session->setFlash(__('Invalid campus', true));
			$this->redirect(array('action' => 'index'));
		}

		$this->set('campus', $this->Campus->read(null, $id));

		// get the most recent change
		$this->set('revision', $this->Campus->revision($id));
	}

/**
 * Revises a campus (confirm or deny revision)
 *
 * @param integer $id The id of the campus
 * @param boolean $confirm Whether or not to approve the revision
 */
	function revise($confirm = false) {
		$id = $this->passedArgs['Campus'];

		if ($confirm) {
			$success = $this->Campus->confirmRevision($id);
		} else {
			$success = $this->Campus->denyRevision($id);
		}

		if ($success) {
			$this->Session->setFlash('Action taken');
		} else {
			$this->Session->setFlash('Error');
		}

		$this->redirect(array('action' => 'history', 'Campus' => $id));
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