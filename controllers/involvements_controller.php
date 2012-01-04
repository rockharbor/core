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
		),
		'MultiSelect.MultiSelect'
	);
	
/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */
	function beforeFilter() {
		$this->_editSelf('index');
		parent::beforeFilter();
	}
	
/**
 * Shows a list of involvement opportunities
 */	
	function index($viewStyle = 'column') {
		$private = $this->Involvement->Roster->User->Group->canSeePrivate($this->activeUser['Group']['id']);

		$subministries = $this->Involvement->Ministry->children($this->passedArgs['Ministry']);
		$ids = Set::extract('/Ministry/id', $subministries);
		array_unshift($ids, $this->passedArgs['Ministry']);
		
		$conditions['or']['Involvement.ministry_id'] = $ids;
		if (empty($this->data) || !$this->data['Involvement']['inactive']) {
			$conditions['Involvement.active'] = true;
		}
		if (empty($this->data) || !$this->data['Involvement']['private']) {
			$conditions['Involvement.private'] = false;
		} elseif (!$private) {
			$signedUp = array();
			if (isset($this->passedArgs['User'])) {
				$signedUp = $this->Involvement->Roster->find('all', array(
					'conditions' => array(
						'user_id' => $this->passedArgs['User']
					)
				));
			}
			$conditions[]['or'] = array(
				'Involvement.private' => false,
				'Involvement.id' => Set::extract('/Roster/involvement_id', $signedUp)
			);
		}
		if (empty($this->data) || !$this->data['Involvement']['passed']) {
			$db = $this->Involvement->getDataSource();
			$conditions[] = $db->expression('('.$this->Involvement->getVirtualField('passed').') = false');
		}
		
		$displayInvolvements = $this->Involvement->Ministry->find('all', array(
			'fields' => array('id'),
			'conditions' => array(
				'id' => $ids
			),
			'contain' => array(
				'DisplayInvolvement' => array(
					'fields' => array('id')
				)
			)
		));
		$ids = Set::extract('/DisplayInvolvement/id', $displayInvolvements);
		$conditions['or']['Involvement.id'] = $ids;

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
			'limit' => $viewStyle == 'column' ? 6 : 20,
			'order' => 'Ministry.name ASC, Involvement.name ASC'
		);

		$involvements = $this->FilterPagination->paginate();

		foreach ($involvements as &$involvement) {
			$involvement['dates'] = $this->Involvement->Date->generateDates($involvement['Involvement']['id'], array('limit' => 1));
		}

		$this->set(compact('viewStyle', 'involvements'));
	}
	
/**
 * Views an involvement opportunity
 */
	function view() {
		$id = $this->passedArgs['Involvement'];
		
		if (!$id) {
			$this->cakeError('error404');
		}
		
		$this->Involvement->contain(array(
			'InvolvementType',
			'Ministry' => array(
				'Campus'
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
				'User' => array(
					'Profile' => array(
						'fields' => array('name', 'user_id', 'id'),
					)
				)
			),
			'Image',
			'Document'
		));
		$involvement = $this->Involvement->read(null, $id);
		$involvement['Date'] = $this->Involvement->Date->generateDates($id, array('limit' => 5));

		$inRoster = $this->Involvement->Roster->hasAny(array(
			'user_id' => $this->activeUser['User']['id'],
			'involvement_id' => $id
		));
		
		if ($involvement['Involvement']['private'] && !$this->Involvement->Roster->User->Group->canSeePrivate($this->activeUser['Group']['id']) && !$inRoster) {
			$this->cakeError('privateItem', array('type' => 'Involvement'));
		}

		$householdMembers = $this->Involvement->Roster->User->HouseholdMember->Household->getMemberIds($this->activeUser['User']['id']);
		$householdMembers[] = $this->activeUser['User']['id'];
		$signedUp = $this->Involvement->Roster->find('all', array(
			'conditions' => array(
				'Roster.user_id' => $householdMembers,
				'Roster.involvement_id' => $involvement['Involvement']['id']
			),
			'contain' => array(
				'User' => array(
					'Profile' => array(
						'fields' => array('name', 'user_id', 'id'),
					)
				)
			)
		));
		
		$this->set(compact('involvement', 'signedUp', 'inRoster'));
	}

/**
 * Invites a roster to an involvement opportunity
 *
 * ### Passed args:
 * - integer `Involvement` The involvement id to get the roster from
 * 
 * @param integer $mskey The multiselect key
 * @param boolean $add Whether to add or invite
 * @todo Don't invite users who are already on the roster! (move to model?)
 */
	function invite_roster($mskey = null, $status = 3) {
		// get users from roster
		$roster = $this->Involvement->Roster->find('all', array(
			'fields' => array(
				'user_id'
			),
			'conditions' => array(
				'involvement_id' => $this->passedArgs['Involvement']
			)
		));
		$userIds = Set::extract('/Roster/user_id', $roster);
		
		$this->Involvement->Roster->User->contain(array('Profile'));
		$this->set('notifier', $this->Involvement->Roster->User->read(null, $this->activeUser['User']['id']));

		$toInvolvements = $this->MultiSelect->getSelected($mskey);
		foreach ($toInvolvements as $to) {
			$this->Involvement->contain(array('InvolvementType', 'PaymentOption'));
			$involvement = $this->Involvement->read(null, $to);
			if (count($involvement['PaymentOption']) > 0) {
				$paymentOption = $involvement['PaymentOption'][0]['id'];
			} else {
				$paymentOption = null;
			}
			foreach ($userIds as $userId) {
				$roster = $this->Involvement->Roster->setDefaultData(array(
					'roster' => array(
						'Roster' => array(
							'user_id' => $userId
						)
					),
					'involvement' => $involvement,
					'defaults' => array(
						'pay_later' => true
					)
				));
				$roster['Roster']['roster_status_id'] = $status;
				
				$this->Involvement->Roster->create();
				if ($this->Involvement->Roster->save($roster)) {
					$this->set('involvement', $involvement);
					if ($status == 1) {
						$this->Notifier->notify(
							array(
								'to' => $userId,
								'template' => 'involvements_invite_'.$status,
								'You\'ve been invited to join '.$involvement['Involement']['name']
							)
						);
					} else {
						$this->Notifier->invite(
							array(
								'to' => $userId,
								'template' => 'involvements_invite_'.$status,
								'confirm' => '/rosters/status/'.$this->Involvement->Roster->id.'/1',
								'deny' => '/rosters/status/'.$this->Involvement->Roster->id.'/4' //status 4 = declined
							)
						);
					}
				}
			}
		}
		$this->Session->setFlash('Your invitation has been sent.', 'flash'.DS.'success');
		
		$this->redirect($this->referer());
	}
	
/**
 * Invites a user to an involvement opportunity
 *
 * ### Passed args:
 * - `Involvement` The involvement id to invite the user to
 *
 * @param integer $mskey The multiselect key
 * @param boolean $add Whether to add or invite
 * @todo Don't invite users who are already on the roster! (move to model?)
 */ 
	function invite($mskey = null, $status = 3) {
		$this->Involvement->Roster->User->contain(array('Profile'));
		$this->Involvement->contain(array('InvolvementType'));
		
		$involvement = $this->Involvement->read(null, $this->passedArgs['Involvement']);
		$this->set('notifier', $this->Involvement->Roster->User->read(null, $this->activeUser['User']['id']));
		
		$userIds = $this->MultiSelect->getSelected($mskey);
		foreach ($userIds as $userId) {
			$roster = $this->Involvement->Roster->setDefaultData(array(
				'roster' => array(
					'Roster' => array(
						'user_id' => $userId
					)
				),
				'involvement' => $involvement,
				'defaults' => array(
					'pay_later' => true
				)
			));
			$roster['Roster']['roster_status_id'] = $status;

			$this->Involvement->Roster->create();
			if ($this->Involvement->Roster->save($roster)) {
				$this->set('involvement', $involvement);
				$this->Notifier->invite(
					array(
						'to' => $userId,
						'template' => 'involvements_invite_'.$status,
						'confirm' => '/rosters/status/'.$this->Involvement->Roster->id.'/1',
						'deny' => '/rosters/status/'.$this->Involvement->Roster->id.'/4' //status 4 = declined
					)
				);
			}
		}
		$this->Session->setFlash('Your invitation has been sent.', 'flash'.DS.'success');

		$this->redirect($this->referer());
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
				$this->Session->setFlash('This involvement opportunity has been created.', 'flash'.DS.'success');
				$this->redirect(array('action' => 'edit', 'Involvement' => $this->Involvement->id));
			} else {
				$this->Session->setFlash('Unable to create involvement opportunity. Please try again.', 'flash'.DS.'failure');
			}
		}
				
		$this->set('ministries', $this->Involvement->Ministry->active('list'));
		$this->set('displayMinistries', array($this->Involvement->Ministry->active('list')));
		$this->set('involvementTypes', $this->Involvement->InvolvementType->find('list'));
	}
	
/**
 * Edits an involvement opportunity
 */
	function edit() {
		$id = $this->passedArgs['Involvement'];
	
		if (!$id && empty($this->data)) {
			$this->cakeError('error404');
		}

		if (!empty($this->data)) {
			if ($this->Involvement->save($this->data)) {
				$this->Session->setFlash('This involvement opportunity has been updated.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Unable to update involvement opportunity. Please try again.', 'flash'.DS.'failure');
			}	
		}
		if (empty($this->data)) {
			$this->Involvement->contain(array('Ministry'));
			$this->data = $this->Involvement->read(null, $id);			
		}
		
		$this->set('ministries', $this->Involvement->Ministry->active('list'));
		$this->set('displayMinistries', array($this->Involvement->Ministry->active('list')));
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
			$this->cakeError('error404');
		}
		
		// get involvement
		$this->Involvement->contain(array('PaymentOption', 'Leader'));
		$involvement = $this->Involvement->read(null, $id);
		if ($involvement['Involvement']['take_payment'] && $active) {
			if (empty($involvement['PaymentOption'])) {
				$this->Session->setFlash($involvement['Involvement']['name'].' cannot be activated until a payment option is created.', 'flash'.DS.'failure');
				$this->redirect($this->referer());
				return;
			}
		}
		if (empty($involvement['Leader']) && $active) {
			$this->Session->setFlash($involvement['Involvement']['name'].' cannot be activated until a leader has been assigned.', 'flash'.DS.'failure');
			$this->redirect($this->referer());
			return;
		}
		
		$success = $this->Involvement->toggleActivity($id, $active, $recursive);
		
		if ($success) {
			$this->Session->setFlash(
				'Successfully '.($active ? 'activated' : 'deactivated')
				.' this involvement opportunity.',
				'flash'.DS.'success'
			);
		} else {
			$this->Session->setFlash(
				'Unable to '.($active ? 'activate' : 'deactivate')
				.' this invovlement opportunity.',
				'flash'.DS.'failure'
			);
		}
		$this->data = array();
		$this->redirect($this->referer());
	}
	
/**
 * Deletes an involvement opportunity
 *
 * @param integer $id The id of the involvement to delete
 */
	function delete($id = null) {
		if (!$id) {
			$this->cakeError('error404');
		}
		if ($this->Involvement->delete($id)) {
			$this->Session->setFlash('This involvement opportunity has been deleted.', 'flash'.DS.'success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('Unable to delete involvement opportunity. Please try again.', 'flash'.DS.'failure');
		$this->redirect(array('action' => 'index'));
	}
}
?>