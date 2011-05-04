<?php
/**
 * Involvement controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Involvements Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class InvolvementsController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Involvements';

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array('Formatting');

/**
 * Extra components for this controller
 *
 * @var array
 */
	var $components = array(
		'FilterPagination' => array(
			'startEmpty' => false
		)
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
		$this->set('_canViewRoster', $this->isAuthorized('rosters/index'));
		$this->set('_canEmail', $this->isAuthorized('sys_emails/compose'));
		$this->set('_canViewMap', $this->isAuthorized('reports/map'));
		$this->set('_canRemove', $this->isAuthorized('rosters/delete'));
		$this->set('_canConfirm', $this->isAuthorized('rosters/confirm'));
	}
	
/**
 * Shows a list of involvement opportunities
 */	
	function index($viewStyle = 'column') {
		$private = $this->Involvement->Roster->User->Group->canSeePrivate($this->activeUser['Group']['id']);
		$inactive = $private;

		$conditions = array('Involvement.ministry_id' => $this->passedArgs['Ministry']);
		if (empty($this->data) || !$this->data['Involvement']['inactive']) {
			$conditions['Involvement.active'] = true;
			$db = $this->Involvement->getDataSource();
			$conditions[] = $db->expression('('.$this->Involvement->getVirtualField('passed').') = false');
		}
		if (empty($this->data) || !$this->data['Involvement']['private']) {
			$conditions['Involvement.private'] = false;
		}

		$this->paginate = array(
			'contain' => array(
				'Ministry' => array(
					'fields' => array('id', 'name'),
					'Campus' => array(
						'fields' => array('id', 'name')
					),
					'ParentMinistry' => array(
						'fields' => array('id', 'name')
					)
				)
			),
			'conditions' => $conditions,
			'limit' => $viewStyle == 'column' ? 6 : 20
		);

		$involvements = $this->FilterPagination->paginate();

		foreach ($involvements as &$involvement) {
			$involvement['dates'] = $this->Involvement->Date->generateDates($involvement['Involvement']['id'], array('limit' => 1));
		}

		$this->set(compact('viewStyle', 'involvements', 'private', 'inactive'));
	}
	
/**
 * Views an involvement opportunity
 */
	function view() {
		$id = $this->passedArgs['Involvement'];
		
		if (!$id) {
			$this->Session->setFlash('Invalid involvement');
			$this->redirect(array('action' => 'index'));
		}
		$householdMembers = $this->Involvement->Roster->User->HouseholdMember->Household->getMemberIds($this->activeUser['User']['id']);
		$householdMembers[] = $this->activeUser['User']['id'];
		$this->Involvement->contain(array(
			'InvolvementType',
			'Date',
			'Ministry' => array(
				'Campus',
				'Image'
			),
			'Leader' => array(
				'User' => array(
					'Profile' => array(
						'fields' => array('name', 'primary_email')
					)
				)
			),
			'Address',
			'Roster' => array(
				'conditions' => array(
					'Roster.user_id' => $householdMembers
				),
				'User' => array(
					'Profile' => array(
						'fields' => array('name', 'user_id', 'id'),
					)
				)
			)
		));
		$involvement = $this->Involvement->read(null, $id);
		$involvement['Date'] = $this->Involvement->Date->generateDates($id, array('limit' => 5));

		if ($involvement['Involvement']['private'] && !$this->Involvement->Roster->User->Group->canSeePrivate($this->activeUser['Group']['id'])) {
			$this->Session->setFlash('That Involvement is private', 'flash'.DS.'failure');
			$this->redirect(array('action' => 'index'));
		}

		$this->set(compact('involvement'));
	}

/**
 * Invites a roster to an involvement opportunity
 *
 * ### Passed args:
 * - integer `Involvement` The involvement id to get the roster from
 * 
 * @param integer $to The involvement to invite them to
 * @todo Don't invite users who are already on the roster! (move to model?)
 */
	function invite_roster($to = null) {
		$from = $this->passedArgs['Involvement'];

		// get from roster
		$this->Involvement->contain(array('Roster'));
		$roster = $this->Involvement->read(null, $from);
		$userIds = Set::extract('/Roster/user_id', $roster);
		
		$this->Involvement->Roster->User->contain(array('Profile'));
		$this->set('notifier', $this->Involvement->Roster->User->read(null, $this->activeUser['User']['id']));

		foreach ($userIds as $userId) {
			$this->Involvement->contain(array('InvolvementType'));
			$this->set('involvement', $this->Involvement->read(null, $to));
			$this->Notifier->notify(
				array(
					'to' => $userId,
					'type' => 'invitation',
					'template' => 'involvements_invite',
					'subject' => 'Invitation'
				)
			);
			$this->Session->setFlash('The user was invited.', 'flash'.DS.'success');			
		}
		
		$this->redirect(array('action' => 'view', $to));
	}
	
/**
 * Invites a user to an involvement opportunity
 *
 * ### Passed args:
 * - `Involvement` The involvement id to invite the user to
 *
 * @param integer $userId The user to invite
 * @todo Don't invite users who are already on the roster! (move to model?)
 */ 
	function invite($userId = null) {
		$involvementId = $this->passedArgs['Involvement'];

		// create notification from template
		$this->Involvement->Roster->User->contain(array('Profile'));
		$this->Involvement->contain(array('InvolvementType'));
		$this->set('notifier', $this->Involvement->Roster->User->read(null, $this->activeUser['User']['id']));
		$this->set('involvement', $this->Involvement->read(null, $involvementId));

		$this->Notifier->notify(
			array(
				'to' => $userId,
				'type' => 'invitation',
				'template' => 'involvements_invite',
				'subject' => 'Invitation'
			)
		);
		$this->redirect(array('action' => 'view', $involvementId));
	}
	
/**
 * Adds an involvement opportunity
 *
 * By default, Involvement is inactive until Involvement::toggleActivity() is called. Additional
 * validation is performed then.
 */
	function add() {		
		if (!empty($this->data)) {
			$this->Involvement->create();
			if ($this->Involvement->save($this->data)) {
				$this->Session->setFlash('Involvement opportunity saved!', 'flash'.DS.'success');
				$this->redirect(array('action' => 'edit', 'Involvement' => $this->Involvement->id));
			} else {
				$this->Session->setFlash('The involvement could not be saved. Please, try again.', 'flash'.DS.'failure');
			}
		}
				
		$this->set('ministries', $this->Involvement->Ministry->find('list'));
		$this->set('displayMinistries', array($this->Involvement->Ministry->find('list')));
		$this->set('involvementTypes', $this->Involvement->InvolvementType->find('list'));
	}
	
/**
 * Edits an involvement opportunity
 */
	function edit() {
		$id = $this->passedArgs['Involvement'];
	
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Invalid involvement');
			$this->redirect(array('action' => 'index'));
		}

		if (!empty($this->data)) {
			if ($this->Involvement->save($this->data)) {
				$this->Session->setFlash('Updated Involvement!', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('There were problems saving the changes.', 'flash'.DS.'failure');
			}	
		}
		if (empty($this->data)) {
			$this->data = $this->Involvement->read(null, $id);			
		}
		
		$this->set('ministries', $this->Involvement->Ministry->find('list', array(
			'conditions' => array(
				'Ministry.active' => true,
			),
			'order' => 'Ministry.name'
		)));
		$this->set('displayMinistries', array($this->Involvement->Ministry->find('list')));
		$this->set('involvementTypes', $this->Involvement->InvolvementType->find('list'));
	}

/**
 * Toggles the `active` field for an involvement
 *
 * ### Requirements:
 * - At least 1 leader must be added
 * - If the involvement takes payment, at least 1 PaymentOption must be defined
 *
 * @param boolean $active Whether to make the model inactive or active
 * @param boolean $recursive Whether to iterate through the model's relationships and mark them as $active
 */
	function toggle_activity($active = false, $recursive = false) {
		$id = $this->passedArgs['Involvement'];
		
		if (!$id) {
			$this->Session->setFlash('Invalid id', 'flash'.DS.'failure');
			$this->redirect(array('action' => 'edit', $id));
		}
		
		// get involvement
		$this->Involvement->contain(array('PaymentOption', 'Leader'));
		$involvement = $this->Involvement->read(null, $id);
		if ($involvement['Involvement']['take_payment'] && $active) {
			if (empty($involvement['PaymentOption'])) {
				$this->Session->setFlash('Cannot activate until a payment option is defined', 'flash'.DS.'failure');
				$this->redirect($this->referer());
				return;
			}
		}
		if (empty($involvement['Leader']) && $active) {
			$this->Session->setFlash('Cannot activate until a leader is added', 'flash'.DS.'failure');
			$this->redirect($this->referer());
			return;
		}
		
		$success = $this->Involvement->toggleActivity($id, $active, $recursive);
		
		if ($success) {
			$this->Session->setFlash(
				'Successfully '.($active ? 'activated' : 'deactivated')				
				.' Involvement '.$id.' '
				.($recursive ? ' and all related items' : ''),
				'flash'.DS.'success'
			);
		} else {
			$this->Session->setFlash(
				'Failed to '.($active ? 'activate' : 'deactivate')				
				.' Involvement '.$id.' '
				.($recursive ? ' and all related items' : ''),
				'flash'.DS.'failure'
			);
		}
		$this->data = array();
		$this->redirect($this->emptyPage);
	}
	
/**
 * Deletes an involvement opportunity
 *
 * @param integer $id The id of the involvement to delete
 */
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for involvement', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Involvement->delete($id)) {
			$this->Session->setFlash(__('Involvement deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Involvement was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>