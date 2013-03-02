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
	public $name = 'Involvements';

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	public $helpers = array('Formatting');

/**
 * Extra components for this controller
 *
 * @var array
 */
	public $components = array(
		'FilterPagination' => array(
			'startEmpty' => false
		),
		'MultiSelect.MultiSelect'
	);

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 */
	public function beforeFilter() {
		$this->_editSelf('index');
		parent::beforeFilter();
	}

/**
 * Shows a list of involvement opportunities
 */
	public function index($viewStyle = 'column') {
		$private = $this->Involvement->Roster->User->Group->canSeePrivate($this->activeUser['Group']['id']);

		$conditions['or']['Involvement.ministry_id'] = $this->passedArgs['Ministry'];

		if (empty($this->data)) {
			$this->data = array(
				'Involvement' => array(
					'inactive' => 0,
					'private' => 0,
					'previous' => 0
				)
			);
		}

		// set conditions based on filters
		if ($this->data['Involvement']['inactive']) {
			$conditions['Involvement.active'] = array(0, 1);
		} else {
			$conditions['Involvement.active'] = 1;
		}
		if ($this->data['Involvement']['private'] && $private) {
			$conditions['Involvement.private'] = array(0, 1);
		} else {
			$conditions['Involvement.private'] = 0;
		}
		if (!$this->data['Involvement']['previous']) {
			$db = $this->Involvement->getDataSource();
			$conditions[] = $db->expression('NOT ('.$this->Involvement->getVirtualField('previous').')');
		}

		// include display involvements
		$ids = array();
		$displayInvolvements = $this->Involvement->Ministry->find('all', array(
			'fields' => array('id'),
			'conditions' => array(
				'id' => $this->passedArgs['Ministry']
			),
			'contain' => array(
				'DisplayInvolvement' => array(
					'fields' => array('id')
				)
			)
		));
		$ids = array_merge($ids, Set::extract('/DisplayInvolvement/id', $displayInvolvements));

		if (!empty($ids)) {
			$conditions['or']['Involvement.id'] = array_unique($ids);
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
			'limit' => $viewStyle == 'column' ? 6 : 20,
			'order' => 'Ministry.name ASC, Involvement.name ASC'
		);

		$involvements = $this->FilterPagination->paginate('Involvement');

		foreach ($involvements as &$involvement) {
			$involvement['dates'] = $this->Involvement->Date->generateDates($involvement['Involvement']['id'], array(
				'limit' => 1,
				'start' => strtotime('now')
			));
		}

		$this->set(compact('viewStyle', 'involvements', 'private'));
	}

/**
 * Views an involvement opportunity
 */
	public function view() {
		$id = $this->passedArgs['Involvement'];

		if (!$id) {
			return $this->cakeError('error404');
		}

		$this->Involvement->contain(array(
			'InvolvementType',
			'Ministry' => array(
				'ParentMinistry',
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
		$involvement['Date'] = $this->Involvement->Date->generateDates($id, array(
			'limit' => 5,
			'start' => strtotime('now')
		));

		$roster = $this->Involvement->Roster->find('first', array(
			'fields' => array(
				'roster_status_id'
			),
			'conditions' => array(
				'user_id' => $this->activeUser['User']['id'],
				'involvement_id' => $id
			)
		));
		$inRoster = !empty($roster);
		$canSeeRoster =
			($inRoster && $roster['Roster']['roster_status_id'] == 1 && $involvement['Involvement']['roster_visible'])
			|| $this->Involvement->isLeader($this->activeUser['User']['id'], $id)
			|| $this->Involvement->Ministry->isManager($this->activeUser['User']['id'], $involvement['Involvement']['ministry_id'])
			|| $this->Involvement->Ministry->Campus->isManager($this->activeUser['User']['id'], $involvement['Ministry']['campus_id'])
			|| $this->isAuthorized('rosters/index', array('Involvement' => $id));

		if ($involvement['Involvement']['private']
			&& !$this->Involvement->Roster->User->Group->canSeePrivate($this->activeUser['Group']['id'])
			&& !$inRoster
			&& !$this->Involvement->isLeader($this->activeUser['User']['id'], $id)
		) {
			return $this->cakeError('privateItem', array('type' => 'Involvement'));
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

		$full = false;
		if (!empty($involvement['Involvement']['roster_limit'])) {
			$currentCount = $this->Involvement->Roster->find('count', array(
				'conditions' => array(
					'Roster.involvement_id' => $involvement['Involvement']['id'],
					'Roster.roster_status_id' => 1
				),
				'contain' => false
			));

			$full = $currentCount >= $involvement['Involvement']['roster_limit'];
		}

		$this->set(compact('involvement', 'signedUp', 'inRoster', 'canSeeRoster', 'full'));
	}

/**
 * Invites a roster to an involvement opportunity
 *
 * ### Passed args:
 * - integer `Involvement` The involvement id to get the roster from
 *
 * @param boolean $add Whether to add or invite
 * @todo Don't invite users who are already on the roster! (move to model?)
 */
	public function invite_roster($status = 3) {
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

		$this->Involvement->contain(array('InvolvementType'));
		$fromInvolvement = $this->Involvement->read(null, $this->passedArgs['Involvement']);
		$toInvolvements = $this->_extractIds();
		$this->set('fromInvolvement', $fromInvolvement);
		foreach ($toInvolvements as $to) {
			$this->Involvement->contain(array('InvolvementType', 'PaymentOption'));
			$involvement = $this->Involvement->read(null, $to);
			if (count($involvement['PaymentOption']) > 0) {
				$paymentOption = $involvement['PaymentOption'][0]['id'];
			} else {
				$paymentOption = null;
			}
			$leaders = $this->Involvement->getLeaders($to);
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
					$invitee = $this->Involvement->Roster->User->Profile->findByUserId($userId);
					$this->set('invitee', $invitee);

					if ($status == 1) {
						$subject = $invitee['Profile']['name'].' has been added to '.$involvement['Involvement']['name'];
						$this->Notifier->notify(array(
							'to' => $userId,
							'template' => 'involvements_invite_'.$status,
							'subject' => 'You\'ve been added to '.$involvement['Involvement']['name']
						));
					} else {
						$subject = $invitee['Profile']['name'].' has been invited to '.$involvement['Involvement']['name'];
						$this->Notifier->invite(array(
							'to' => $userId,
							'template' => 'involvements_invite_'.$status,
							'confirm' => '/rosters/status/'.$this->Involvement->Roster->id.'/1',
							'deny' => '/rosters/status/'.$this->Involvement->Roster->id.'/4' //status 4 = declined
						));
					}
				}
			}
			foreach ($leaders as $leader) {
				$this->Notifier->notify(array(
					'to' => $leader,
					'template' => 'involvements_invite_roster_'.$status.'_leader',
					'subject' => $subject
				));
			}
		}
		$this->Session->setFlash($subject, 'flash'.DS.'success');

		$this->redirect($this->referer());
	}

/**
 * Invites a user to an involvement opportunity
 *
 * ### Passed args:
 * - `Involvement` The involvement id to invite the user to
 *
 * @param boolean $add Whether to add or invite
 * @todo Don't invite users who are already on the roster! (move to model?)
 */
	public function invite($status = 3) {
		$this->Involvement->Roster->User->contain(array('Profile'));
		$this->Involvement->contain(array('InvolvementType'));

		$involvement = $this->Involvement->read(null, $this->passedArgs['Involvement']);
		$leaders = $this->Involvement->getLeaders($involvement['Involvement']['id']);

		$userIds = $this->_extractIds();
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
				$invitee = $this->Involvement->Roster->User->Profile->findByUserId($userId);
				$this->set('invitee', $invitee);

				if ($status == 1) {
					$subject = $invitee['Profile']['name'].' has been added to '.$involvement['Involvement']['name'];
					$this->Notifier->notify(array(
						'to' => $userId,
						'template' => 'involvements_invite_'.$status,
						'subject' => 'You\'ve been added to '.$involvement['Involvement']['name']
					));
				} else {
					$subject = $invitee['Profile']['name'].' has been invited to '.$involvement['Involvement']['name'];
					$this->Notifier->invite(array(
						'to' => $userId,
						'template' => 'involvements_invite_'.$status,
						'confirm' => '/rosters/status/'.$this->Involvement->Roster->id.'/1',
						'deny' => '/rosters/status/'.$this->Involvement->Roster->id.'/4' //status 4 = declined
					));
				}

				foreach ($leaders as $leader) {
					$this->Notifier->notify(array(
						'to' => $leader,
						'template' => 'involvements_invite_'.$status.'_leader',
						'subject' => $subject
					));
				}
			}
		}
		$this->Session->setFlash($subject, 'flash'.DS.'success');

		$this->redirect($this->referer());
	}

/**
 * Adds an involvement opportunity
 *
 * By default, Involvement is inactive until Involvement::toggleActivity() is called. Additional
 * validation is performed then.
 */
	public function add() {
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
		$this->set('defaultStatuses', $this->Involvement->DefaultStatus->find('list'));
	}

/**
 * Edits an involvement opportunity
 */
	public function edit() {
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
		$this->set('defaultStatuses', $this->Involvement->DefaultStatus->find('list'));
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
	public function toggle_activity($active = false, $recursive = false) {
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
 */
	public function delete() {
		$id = $this->passedArgs['Involvement'];
		if (!$id) {
			$this->cakeError('error404');
		}
		$ministry = $this->Involvement->read(array('ministry_id'), $id);
		if ($this->Involvement->delete($id)) {
			$this->Session->setFlash('This involvement opportunity has been deleted.', 'flash'.DS.'success');
			$this->redirect(array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $ministry['Involvement']['ministry_id']));
		}
		$this->Session->setFlash('Unable to delete involvement opportunity. Please try again.', 'flash'.DS.'failure');
		$this->redirect(array('controller' => 'ministries', 'action' => 'view', 'Ministry' => $ministry['Involvement']['ministry_id']));
	}
}
