<?php
/**
 * Household controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Households Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class HouseholdsController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Households';

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
 * Extra components for this controller
 * 
 * @var array
 */
	var $components = array(
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
		parent::beforeFilter();
		$this->_editSelf('index', 'delete', 'confirm');
	}

/**
 * Confirms a user's addition to the household
 *
 * @param integer $user The user id
 * @param integer $household The household id
 */
	function confirm($user, $household) {
		$viewUser = $this->passedArgs['User'];
		
		if ($this->Household->join($household, $user, true)) {
			$joinedHousehold = $this->Household->read(null, $household);
			$contact = $this->Household->HouseholdMember->User->Profile->findByUserId($joinedHousehold['Household']['contact_id']);
			$joined = $this->Household->HouseholdMember->User->Profile->findByUserId($user);
			$this->set(compact('contact', 'joined'));
			$subject = $joined['Profile']['name'].' joined '.$contact['Profile']['name'].'\'s household.';
			$this->Notifier->notify(array(
				'to' => $user,
				'template' => 'households_join',
				'subject' => $subject
			));
			$success = true;
			$this->Session->setFlash($subject, 'flash'.DS.'success');
		} else {
			$success = false;
			$this->Session->setFlash('Unable to process request. Please try again.', 'flash'.DS.'failure');
		}

		if (isset($this->params['requested']) && $this->params['requested']) {
			return $success;
		}
		$this->redirect(array(
			'action' => 'index',
			'User' => $viewUser
		));
	}

/**
 * Invites a user to a houshold
 *
 * @param integer $userId The user id
 * @param integer $household The id of the household
 */ 
	function invite($userId, $household) {
		$this->Household->contain(array('HouseholdContact' => array('Profile')));
		$contact = $this->Household->read(null, $household);
		$this->set('contact', $contact['HouseholdContact']);
		
		$usersInvited = array();
		// check to see if they are in this household
		$householdMember = $this->Household->HouseholdMember->find('first', array(
			'conditions' => array(
				'household_id' => $household,
				'user_id' => $userId
			)
		));

		$this->Household->HouseholdContact->contain(array('Profile'));
		$user = $this->Household->HouseholdContact->read(null, $userId);

		if (empty($householdMember)) {			
			// add them to the household if it exists
			$this->Household->id = $household;
			if ($this->Household->exists($household)) {
				$addUser = $this->Household->HouseholdMember->User->find('first', array(
					'conditions' => array(	
						'User.id' => $userId
					),
					'contain' => 'Profile'
				));
				$this->Household->HouseholdContact->contain(array('Profile'));

				$success = $this->Household->join(
					$household,
					$userId,
					false
				);

				if ($success) {
					$this->Notifier->invite(
						array(
							'to' => $userId,
							'template' => 'households_invite',
							'confirm' => '/households/confirm/'.$userId.'/'.$household,
							'deny' => '/households/delete/'.$userId.'/'.$household,
						)
					);
					$success = true;
					$this->Session->setFlash($addUser['Profile']['name'].' has been invited to this household.', 'flash'.DS.'success');
				} else {
					$success = true;
					$this->Session->setFlash('Unable to invite '.$addUser['Profile']['name'].' to this household. Please try again.', 'flash'.DS.'failure');
				}
			} else {
				$success = false;
				$this->Session->setFlash('Invalid Id.');
			}
		}
		
		if (isset($this->params['requested']) && $this->params['requested']) {
			return $success;
		}
		
		$this->redirect(array(
			'controller' => 'pages',
			'action' => 'message'
		));
	}

/**
 * Removes a user from a household
 *
 * @param integer $userId The user id
 * @param integer $household The id of the household
 */ 
	function delete($userId, $household) {
		$householdMember = $this->Household->HouseholdMember->find('first', array(
			'conditions' => array(
				'household_id' => $household,
				'user_id' => $userId
			)
		));
		// remove household member record
		$dSuccess = $this->Household->HouseholdMember->delete($householdMember['HouseholdMember']['id']);

		// add user to a household (function will check if they have one or not)
		$cSuccess = $this->Household->createHousehold($userId);

		$user = $this->Household->HouseholdMember->User->find('first', array(
			'conditions' => array(
				'User.id' => $userId
			),
			'contain' => 'Profile'
		));
		if ($dSuccess && $cSuccess) {
			$leaveHousehold = $this->Household->read(null, $household);
			$leaver = $this->Household->HouseholdMember->User->Profile->findByUserId($userId);
			$contact = $this->Household->HouseholdMember->User->Profile->findByUserId($leaveHousehold['Household']['contact_id']);
			$this->set(compact('leaver', 'contact'));
			$subject = $user['Profile']['name'].' has left this household.';
			$this->Notifier->notify(array(
				'to' => $userId,
				'template' => 'households_remove',
				'subject' => $subject
			));

			$success = true;
			$this->Session->setFlash($subject, 'flash'.DS.'success');
		} else {
			$success = false;
			$this->Session->setFlash('Unable to remove '.$user['Profile']['name'].' from this household. Pleaes try again.', 'flash'.DS.'failure');			
		}
		
		$this->redirect(array(
			'controller' => 'pages',
			'action' => 'message'
		));
	}

/**
 * Changes the household contact
 *
 * @param integer $user The id of the user who is becoming the contact
 * @param integer $household The id of the household to be the contact for
 */ 	
	function make_household_contact($user, $household) {
		$viewUser = $this->passedArgs['User'];
	
		if ($this->Household->makeHouseholdContact($user, $household)) {
			$this->Session->setFlash('Household contact changed!', 'flash'.DS.'success');
		} else {
			$this->Session->setFlash('Error\'d!', 'flash'.DS.'failure');
		}
		
		$this->redirect(array(
			'action' => 'index',
			'User' => $viewUser
		));
	}

/**
 * Shows a list of households for a user
 */ 
	function index() {
		$user = $this->passedArgs['User'];
		
		// get all households this user belongs to
		$householdIds = $this->Household->getHouseholdIds($user, false);
		
		if (empty($householdIds)) {
			$this->Household->createHousehold($user);
			$householdIds = $this->Household->getHouseholdIds($user, false);
		}
	
		$this->set('households', $this->Household->find('all', array(
			'conditions' => array(
				'Household.id' => $householdIds
			),
			'contain' => array(
				'HouseholdMember' => array(
					'User' => array(
						'Profile',
						'Image',
						'Group'
					)
				),
				'HouseholdContact'
			)
		)));	
	}
}
?>