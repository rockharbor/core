<?php
/**
 * Comment controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Comments Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 * @todo Restrict access by group
 */
class CommentsController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Comments';

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array('Formatting');
	
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
 * Shows a list of comments for a user
 */ 
	function index() {
		$viewUser = $this->passedArgs['User'];

		$this->paginate = array(
			'conditions' => array(
				'Comment.user_id' => $viewUser,
				'Group.lft >=' => $this->activeUser['Group']['lft']
			),
			'link' => array(
				'CommentType' => array(
					'Group'
				)
			)
		);
		$this->set('comments', $this->paginate('Comment'));
		
		$this->set('userId', $viewUser);
		$this->set('commentTypes', $this->Comment->CommentType->find('list'));
	}

/**
 * Adds a comment
 */ 
	function add() {		
		$viewUser = $this->passedArgs['User'];
	
		if (!empty($this->data)) {
			$this->data['Comment']['created_by'] = $this->activeUser['User']['id'];
			$this->Comment->create();
			if ($this->Comment->save($this->data)) {
				$this->Session->setFlash('The comment has been saved', 'flash_success');
				$this->redirect(array('action' => 'edit', $this->Comment->getInsertID(), 'User' => $viewUser));
			} else {
				$this->Session->setFlash('The comment could not be saved. Please, try again.', 'flash_failure');
			}
		}
		$this->set('users', $this->Comment->User->find('list'));
		$this->set('commentTypes', $this->Comment->CommentType->find('list', array(
			'conditions' => array(
				'Group.lft >=' => $this->activeUser['Group']['lft'],
				'Group.conditional' => false
			),
			'link' => array(
				'Group'
			)
		)));
		$this->set('userId', $viewUser);
	}

/**
 * Edits a comment
 *
 * @param integer $id The id of the comment to edit
 */ 
	function edit($id = null) {
		$viewUser = $this->passedArgs['User'];
		
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Invalid comment');
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Comment->save($this->data)) {
				$this->Session->setFlash('The comment has been saved', 'flash_success');

			} else {
				$this->Session->setFlash('The comment could not be saved. Please, try again.', 'flash_failure');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Comment->read(null, $id);
		}
		
		$this->set('commentTypes', $this->Comment->CommentType->find('list', array(
			'conditions' => array(
				'Group.lft >=' => $this->activeUser['Group']['lft'],
				'Group.conditional' => false
			),
			'link' => array(
				'Group'
			)
		)));
	}
	
/**
 * Deletes a comment
 *
 * @param integer $id The id of the comment to delete
 */ 
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for comment');
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Comment->delete($id)) {
			$this->Session->setFlash('Comment deleted', 'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('Comment was not deleted', 'flash_failure');
		$this->redirect(array('action' => 'index'));
	}
}
?>