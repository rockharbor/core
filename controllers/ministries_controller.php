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
 * Ministry details
 */ 
	function view() {
		$id = $this->passedArgs['Ministry'];
		
		if (!$id) {
			//404
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
			$this->Session->setFlash('Cannot view '.$ministry['Ministry']['name'].'.', 'flash'.DS.'failure');
			//404
			$this->redirect('/');
		}

		$this->set(compact('ministry'));
	}

/**
 * Adds a ministry
 */ 
	function add() {
		$this->Ministry->Behaviors->disable('Confirm');
		
		if (isset($this->passedArgs['Ministry'])) {
			$this->set('parentId', $this->passedArgs['Ministry']);
			$parentMinistry = $this->Ministry->read(null, $this->passedArgs['Ministry']);
			$this->passedArgs['Campus'] = $parentMinistry['Ministry']['campus_id'];
		}
		
		if (!empty($this->data)) {
			$this->Ministry->create();
			if ($this->Ministry->save($this->data)) {
				$this->Session->setFlash('This ministry has been created.', 'flash'.DS.'success');
				$this->redirect(array('action' => 'view', 'Ministry' => $this->Ministry->id));
			} else {
				$this->Session->setFlash('Unable to create this ministry. Please try again.', 'flash'.DS.'failure');
			}
		}
		
		if (empty($this->data)) {
			$this->data['Ministry']['campus_id'] = $this->passedArgs['Campus'];
		}
		
		$this->set('campuses', $this->Ministry->Campus->find('list'));
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
				$name = $this->Ministry->field('name');
				$this->Ministry->data = $this->data;
				if ($this->Ministry->save()) {
					$this->set('ministry', $this->Ministry->read());
					$this->Notifier->notify(array(
						'to' => Core::read('notifications.ministry_content'),
						'template' => 'ministries_edit',
						'subject' => 'The '.$name.' ministry has been edited'
					));
					$count++;
				}
			}
			$this->Ministry->clearCache();
			$this->Session->setFlash($count.'/'.count($selected).' ministries have been bulk edited.', 'flash'.DS.'success');
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
			//404
			$this->Session->setFlash('Invalid ministry');
			$this->redirect(array('action' => 'index'));
		}

		// if they can confirm a revision, there's no need to go through the confirmation process
		$authorized = false;
		if ($this->isAuthorized('ministries/revise')) {
			$authorized = true;
			$this->Ministry->Behaviors->disable('Confirm');
		}
		
		$this->Ministry->id = $id;
		$name = $this->Ministry->field('name');
		$revision = $this->Ministry->revision($id);
		
		if (!empty($this->data)) {
			if (!$revision) {
				if ($this->Ministry->save($this->data)) {
					if ($authorized) {
						$this->Session->setFlash('This ministry has been saved.', 'flash'.DS.'success');
					} else {
						$this->Session->setFlash('Your changes are pending review.', 'flash'.DS.'success');
					}

					$this->set('ministry', $this->Ministry->read(null, $id));
					$this->Notifier->notify(array(
						'to' => Core::read('notifications.ministry_content'),
						'template' => 'ministries_edit',
						'subject' => 'The '.$name.' ministry has been edited'
					));
				} else {
					$this->Session->setFlash('Unable to save this ministry. Please try again.', 'flash'.DS.'failure');
				}
				
				$revision = $this->Ministry->revision($id);
			} else {
				$this->Session->setFlash('There\'s already a pending change for this ministry.', 'flash'.DS.'failure');
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
			//404
			$this->Session->setFlash('Invalid id', 'flash'.DS.'failure');
			$this->redirect(array('action' => 'edit', $id));
		}

		// get involvement
		$this->Ministry->contain(array('Leader'));
		$ministry = $this->Ministry->read(null, $id);
		if (empty($ministry['Leader']) && $active) {
			$this->Session->setFlash($ministry['Ministry']['name'].' cannot be activated until a leader is assigned.', 'flash'.DS.'failure');
			$this->redirect($this->emptyPage);
			return;
		}

		$this->Ministry->Behaviors->disable('Confirm');
		$success = $this->Ministry->toggleActivity($id, $active, $recursive);
		$this->Ministry->Behaviors->enable('Confirm');

		if ($success) {
			$this->Session->setFlash(
				'Successfully '.($active ? 'activated' : 'deactivated')
				.' this ministry.',
				'flash'.DS.'success'
			);
		} else {
			$this->Session->setFlash(
				'Unable to '.($active ? 'activate' : 'deactivate')
				.' this ministry.',
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
			//404
			$this->Session->setFlash(__('Invalid ministry', true));
			$this->redirect('/');
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
			if ($confirm) {
				$this->Session->setFlash('This request has been approved.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('This request has been denied.', 'flash'.DS.'success');
			}
		} else {
			$this->Session->setFlash('Unable to process this request.', 'flash'.DS.'failure');
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
			//404
			$this->Session->setFlash(__('Invalid id for ministry', true));
			$this->redirect('/');
		}
		$ministry = $this->Ministry->read(null, $id);
		if ($this->Ministry->delete($id)) {
			$this->Session->setFlash('This ministry has been deleted.', 'flash'.DS.'success');
			$this->redirect(array('action' => 'view', 'Ministry' => $ministry['Ministry']['campus_id']));
		}
		$this->Session->setFlash('Unable to delete this ministry. Please try again.', 'flash'.DS.'failure');
		$this->redirect(array('action' => 'view', 'Ministry' => $ministry['Ministry']['campus_id']));
	}
}
?>