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
		// only creators of the comment can edit/delete (unless they have ACL permission)
		if (in_array($this->action, array('edit', 'delete'))) {
			if (isset($this->passedArgs['Comment'])) {
				if ($this->Comment->{'can'.Inflector::camelize($this->action)}($this->activeUser['User']['id'], $this->passedArgs['Comment'])) {
					$this->Auth->allow($this->action);
				}
			}
		}
	}
	
/**
 * Shows a list of comments for a user
 */ 
	function index() {
		$viewUser = $this->passedArgs['User'];

		$groups = $this->Comment->Group->findGroups($this->activeUser['Group']['id']);
		$this->paginate = array(
			'conditions' => array(
				'Comment.user_id' => $viewUser,
				'Comment.group_id' => array_keys($groups)
			),
			'contain' => array(
				'Creator' => array(
					'fields' => array(
						'id', 'group_id'
					),
					'Profile' => array(
						'name',
					),
					'Image'
				)
			),
			'limit' => 5
		);
		$this->set('comments', $this->paginate());
		
		$this->set('userId', $viewUser);
		$this->set('groups', $groups);
	}

/**
 * Adds a comment
 */ 
	function add() {		
		$viewUser = $this->passedArgs['User'];
	
		if (!empty($this->data)) {
			$this->Comment->create();
			if ($this->Comment->save($this->data)) {
				$this->Session->setFlash('The comment has been saved', 'flash'.DS.'success');
				$this->redirect(array('action' => 'index', 'User' => $viewUser));
			} else {
				$this->Session->setFlash('The comment could not be saved. Please, try again.', 'flash'.DS.'failure');
			}
		}
		$groups = $this->Comment->Group->findGroups($this->activeUser['Group']['id']);
		$this->set('groups', $groups);
		$this->set('userId', $viewUser);
	}

/**
 * Edits a comment
 */ 
	function edit() {
		$id = $this->passedArgs['Comment'];
		$viewUser = $this->passedArgs['User'];
		
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Invalid comment');
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Comment->save($this->data)) {
				$this->Session->setFlash('The comment has been saved', 'flash'.DS.'success');

			} else {
				$this->Session->setFlash('The comment could not be saved. Please, try again.', 'flash'.DS.'failure');
			}
		}
		if (empty($this->data)) {
			$this->Comment->contain(array(
				'Group'
			));
			$this->data = $this->Comment->read(null, $id);
		}

		$groups = $this->Comment->Group->findGroups($this->activeUser['Group']['id']);
		$this->set('groups', $groups);
	}
	
/**
 * Deletes a comment
 */ 
	function delete() {
		$viewUser = $this->passedArgs['User'];
		$id = $this->passedArgs['Comment'];

		if (!$id) {
			$this->Session->setFlash('Invalid id for comment');
			$this->redirect(array('action'=>'index', 'User' => $viewUser));
		}
		if ($this->Comment->delete($id)) {
			$this->Session->setFlash('Comment deleted', 'flash'.DS.'success');
			$this->redirect(array('action'=>'index', 'User' => $viewUser));
		}
		$this->Session->setFlash('Comment was not deleted', 'flash'.DS.'failure');
		$this->redirect(array('action' => 'index', 'User' => $viewUser));
	}
}
?>