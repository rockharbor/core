<?php
/**
 * Ministry controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Ministries Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class MinistriesController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Ministries';

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array('Formatting', 'Tree');

/**
 * Extra components for this controller
 *
 * @var array
 */
	var $components = array('MultiSelect.MultiSelect');

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
		
		// if user is leading or managing, let them bulk edit ministries
		if ($this->activeUser['Profile']['leading'] > 0 || $this->activeUser['Profile']['managing'] > 0) {
			$this->Auth->allow('bulk_edit');
		}
	}

/**
 * Shows a list of ministries
 */ 
	function index() {
		$this->Ministry->recursive = 0;

		$this->paginate = array(
			'contain' => array(
				'Involvement',
				'DisplayInvolvement',
				'ChildMinistry',
				'ParentMinistry',
				'Campus'
			)
		);

		$menuConditions = array(
			'order' => 'Ministry.lft ASC'
		);
		if (!$this->Ministry->Leader->User->Group->canSeePrivate($this->activeUser['Group']['id'])) {
			$this->paginate['conditions']['Ministry.private'] = false;
			$this->paginate['contain']['Involvement']['conditions']['Involvement.private'] = false;
			$menuConditions['conditions']['Ministry.private'] = false;
		}

		$this->set('ministries', $this->paginate());
		
		$this->set('ministryMenu', $this->Ministry->find('all', $menuConditions));
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
		
		$ministry = $this->Ministry->find('first', array(
			'conditions' => array(
				'Ministry.id' => $id
			),
			'contain' => array(
				'Campus' => array(
					'fields' => array('id', 'name')
				),
				'ChildMinistry' => array(
					'fields' => array('id', 'name', 'description')
				),
				'ParentMinistry' => array(
					'fields' => array('id', 'name')
				),
				'Image'
			)
		));

		if ($ministry['Ministry']['private'] && !$this->Ministry->Leader->User->Group->canSeePrivate($this->activeUser['Group']['id'])) {
			$this->Session->setFlash('That Ministry is private', 'flash'.DS.'failure');
			$this->redirect(array('action' => 'index'));
		}

		$this->set(compact('ministry'));
	}

/**
 * Adds a ministry
 */ 
	function add() {
		$this->Ministry->Behaviors->disable('Confirm');
		
		if (!empty($this->data)) {
			$this->Ministry->create();
			if ($this->Ministry->save($this->data)) {
				$this->Session->setFlash(__('The ministry has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The ministry could not be saved. Please, try again.', true));
			}
		}
		$this->data['Ministry']['campus_id'] = $this->passedArgs['Campus'];
		
		$this->set('campuses', $this->Ministry->Campus->find('list'));
		$this->set('ministries', $this->Ministry->find('list', array(
			'conditions' => array(
				'Ministry.active' => true,
				'Ministry.parent_id' => null,
				'Ministry.campus_id' => $this->passedArgs['Campus']
			)
		)));
	}

/**
 * Bulk edits ministries
 *
 * @param string $mstoken
 */
	function bulk_edit($mstoken) {
		if (!empty($this->data) && $this->MultiSelect->check($mstoken)) {
			$selected = $this->MultiSelect->getSelected($mstoken);
			$this->Ministry->Behaviors->disable('Confirm');
			$count = 0;
			if (!$this->data['Ministry']['move_ministry']) {
				unset($this->data['Ministry']['parent_id']);
			}
			if (!$this->data['Ministry']['move_campus']) {
				unset($this->data['Ministry']['campus_id']);
			}
			$this->data['Ministry'] = Set::filter($this->data['Ministry']);
			foreach ($selected as $id) {
				if (!$this->isAuthorized('ministries/edit', array('Ministry' => $id))) {
					continue;
				}
				$this->Ministry->create();
				$this->Ministry->id = $id;
				$this->Ministry->data = $this->data;
				if ($this->Ministry->save()) {
					$this->set('ministry', $this->Ministry->read());
					$this->Notifier->notify(array(
						'to' => Core::read('notifications.ministry_content'),
						'template' => 'ministries_edit',
						'subject' => 'Ministry content change',
					));
					$count++;
				}
			}
			$this->Ministry->clearCache();
			$this->Session->setFlash($count.'/'.count($selected).' Ministries have been bulk edited.', 'flash'.DS.'success');
		}
		$this->set('campuses', $this->Ministry->Campus->find('list'));
		$this->set('parents', $this->Ministry->active('list', array(
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
	
		if (!$id) {
			$this->Session->setFlash('Invalid ministry');
			$this->redirect(array('action' => 'index'));
		}

		// if they can confirm a revision, there's no need to go through the confirmation process
		if ($this->isAuthorized('ministries/revise')) {
			$this->Ministry->Behaviors->disable('Confirm');
		}
		
		$this->Ministry->id = $id;
		$revision = $this->Ministry->revision($id);
		
		if (!empty($this->data)) {
			if (!$revision) {
				if ($this->Ministry->save($this->data)) {
					$this->Session->setFlash('The changes to this ministry are pending.', 'flash'.DS.'success');

					$this->set('ministry', $this->Ministry->read(null, $id));
					$this->Notifier->notify(array(
						'to' => Core::read('notifications.ministry_content'),
						'template' => 'ministries_edit',
						'subject' => 'Ministry content change',
					));
				} else {
					$this->Session->setFlash('There were problems saving the changes.', 'flash'.DS.'failure');
				}
				
				$revision = $this->Ministry->revision($id);
			} else {
				$this->Session->setFlash('There\'s already a pending revision for this ministry.', 'flash'.DS.'failure');
			}
		}
		
		$this->data = $this->Ministry->read(null, $id);		
		
		$this->set('campuses', $this->Ministry->Campus->find('list'));
		$this->set('ministries', $this->Ministry->active('list', array(
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
 * Toggles the `active` field for a Ministry
 *
 * ### Requirements:
 * - At least 1 leader must be added
 *
 * @param boolean $active Whether to make the model inactive or active
 * @param boolean $recursive Whether to iterate through the model's relationships and mark them as $active
 */
	function toggle_activity($active = false, $recursive = false) {
		$id = $this->passedArgs['Ministry'];

		if (!$id) {
			$this->Session->setFlash('Invalid id', 'flash'.DS.'failure');
			$this->redirect(array('action' => 'edit', $id));
		}

		// get involvement
		$this->Ministry->contain(array('Leader'));
		$ministry = $this->Ministry->read(null, $id);
		if (empty($ministry['Leader'])) {
			$this->Session->setFlash('Cannot activate until a manager is added', 'flash'.DS.'failure');
			$this->redirect($this->emptyPage);
			return;
		}

		$this->Ministry->Behaviors->disable('Confirm');
		$success = $this->Ministry->toggleActivity($id, $active, $recursive);
		$this->Ministry->Behaviors->enable('Confirm');

		if ($success) {
			$this->Session->setFlash(
				'Successfully '.($active ? 'activated' : 'deactivated')
				.' Ministry '.$id.' '
				.($recursive ? ' and all related items' : ''),
				'flash'.DS.'success'
			);
		} else {
			$this->Session->setFlash(
				'Failed to '.($active ? 'activate' : 'deactivate')
				.' Ministry '.$id.' '
				.($recursive ? ' and all related items' : ''),
				'flash'.DS.'failure'
			);
		}
		$this->data = array();
		$this->redirect($this->emptyPage);
	}
	
/**
 * Displays ministry revision history (up to 1 change)
 *
 * @param integer $id The id of the ministry
 */ 	
	function history() {
		$id = $this->passedArgs['Ministry'];

		if (!$id) {
			$this->Session->setFlash(__('Invalid ministry', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$this->set('ministry', $this->Ministry->read(null, $id));
		
		// get the most recent change (not quite using revisions as defined, but close)
		$this->set('campuses', $this->Ministry->Campus->find('list'));
		$this->set('parents', $this->Ministry->find('list'));
		$this->set('revision', $this->Ministry->revision($id));
	}

/**
 * Revises a ministry (confirm or deny revision)
 *
 * @param integer $id The id of the ministry
 * @param boolean $confirm Whether or not to approve the revision
 */ 	
	function revise($confirm = false) {
		$id = $this->passedArgs['Ministry'];

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
 *
 * @param integer $id The id of the ministry to delete
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