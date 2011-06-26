<?php
/**
 * Invitation controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Invitations Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class InvitationsController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Invitations';
	
/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		$this->_editSelf('index', 'confirm');
		parent::beforeFilter();
	}

/**
 * Views a list of invitations
 */
	function index() {
		$this->paginate = array(
			'conditions' => array(
				'Invitation.id' => $this->Invitation->getInvitations($this->activeUser['User']['id'])
			),
			'order' => array(
				'Invitation.created DESC'
			)
		);
		$this->set('invitations', $this->paginate());
	}

/**
 * Confirms or denies an invitation
 * 
 * @param int $id The invitation id
 * @param boolean $confirm Whether to confirm or deny
 */
	function confirm($id = null, $confirm = 0) {
		$action = $confirm ? 'confirm' : 'deny';
		
		if (!$id) {
			$this->Session->setFlash('Unable to '.$action.' this invitation.', 'flash'.DS.'failure');
			$this->redirect(array('action'=>'index'));
		}
		
		$invitations = $this->Invitation->getInvitations($this->activeUser['User']['id']);
		if (in_array($id, $invitations)) {
			$this->Invitation->id = $id;
			$invitation = $this->Invitation->read();
			
			$success = $this->requestAction($invitation['Invitation'][$action.'_action']);
			if ($success) {
				$action = $action == 'confirm' ? 'confirmed' : 'denied';
				$this->Session->setFlash('You have '.$action.' this invitation.', 'flash'.DS.'success');
				$this->Invitation->delete($id);
			} else {
				$this->Session->setFlash('Unable to '.$action.' this invitation.', 'flash'.DS.'failure');
			}
		} else {
			$this->Session->setFlash('Unable to '.$action.' this invitation.', 'flash'.DS.'failure');
		}
		
		$this->redirect(array('action' => 'index'));
	}
}
?>