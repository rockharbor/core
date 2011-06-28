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
 * Extra helpers for this controller
 * 
 * @var array
 */
	var $helpers = array(
		'SelectOptions',
		'Formatting'
	);
	
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
		$this->paginate = array(
			'contain' => array(
				'Leader'
			)
		);
		$this->set('campuses', $this->paginate());
	}

/**
 * Campus details
 */ 
	function view() {
		$id = $this->passedArgs['Campus'];
		
		if (!$id) {
			//404
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
				$this->Session->setFlash(__('This campus has been created.', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('This campus could not be created. Please try again.', true));
			}
		}
	}

/**
 * Edits a campus
 */ 
	function edit() {
		$id = $this->passedArgs['Campus'];
	
		if (!$id) {
			//404
			$this->Session->setFlash('Invalid campus');
			$this->redirect(array('action' => 'index'));
		}

		// if they can confirm a revision, there's no need to go through the confirmation process
		if ($this->isAuthorized('campuses/revise')) {
			$this->Campus->Behaviors->disable('Confirm');
		}

		$this->Campus->id = $id;
		$name = $this->Campus->field('name');
		$revision = $this->Campus->revision($id);
		
		if (!empty($this->data)) {
			if (!$revision) {
				if ($this->Campus->save($this->data)) {
					$this->Session->setFlash('Your request has been received and is pending approval.', 'flash'.DS.'success');
					$this->set('name', $name);
					$this->Notifier->notify(array(
						'to' => Core::read('notifications.campus_content'),
						'template' => 'campuses_edit',
						'subject' => 'The '.$name.' campus description has been edited'
					));
				} else {
					$this->Campus->setFlash('Unable to save campus. Please try again.', 'flash'.DS.'failure');
				}
				
				$revision = $this->Campus->revision($id);
			} else {
				$this->Session->setFlash('There\'s already an existing request pending approval.', 'flash'.DS.'failure');
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
			//404
			$this->Session->setFlash('Invalid id', 'flash'.DS.'failure');
			$this->redirect(array('action' => 'edit', $id));
		}

		// get involvement
		$this->Campus->contain(array('Leader'));
		$campus = $this->Campus->read(null, $id);
		if (empty($campus['Leader'])) {
			$this->Session->setFlash('Campus cannot be activated until a manager has been assigned.', 'flash'.DS.'failure');
			$this->redirect($this->emptyPage);
			return;
		}

		$this->Campus->Behaviors->disable('Confirm');
		$success = $this->Campus->toggleActivity($id, $active, $recursive);
		$this->Campus->Behaviors->enable('Confirm');

		if ($success) {
			$this->Session->setFlash(
				'Successfully '.($active ? 'activated' : 'deactivated')
				.' this campus'
				.($recursive ? ' and all ministries.' : '.'),
				'flash'.DS.'success'
			);
		} else {
			$this->Session->setFlash(
				'Unable to '.($active ? 'activate' : 'deactivate')
				.' this campus'
				.($recursive ? ' and all ministries.' : '.'),
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
			//404
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
			$this->Session->setFlash(
				'This campus request has been '.($confirm ? 'approved.' : 'denied.'),
				'flash'.DS.'success'
			);
		} else {
			$this->Session->setFlash('Unable to '.($confirm ? 'approve' : 'deny').' campus request. Please try again.');
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
			//404
			$this->Session->setFlash(__('Invalid id for campus', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Campus->delete($id)) {
			$this->Session->setFlash(__('This campus has been deleted.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Unable to delete campus. Please try again.', true));
		$this->redirect(array('action' => 'index'));
	}

}
?>