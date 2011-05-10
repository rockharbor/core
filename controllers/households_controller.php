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
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */
	function beforeFilter() {
		parent::beforeFilter();
		$this->_editSelf('index');
	}

/**
 * Confirms a user's addition to the household
 *
 * @param integer $user The user id
 * @param integer $household The household id
 */
	function confirm($user, $household) {
		$viewUser = $this->passedArgs['User'];
		
		$householdMember = $this->Household->HouseholdMember->find('first', array(
			'conditions' => array(
				'household_id' => $household,
				'user_id' => $user
			)
		));
		$this->Household->HouseholdMember->id = $householdMember['HouseholdMember']['id'];
		$this->Household->HouseholdMember->saveField('confirmed', true);

		$this->Household->contain(array('HouseholdContact' => array('Profile')));
		$contact = $this->Household->read(null, $household);
		$this->set('contact', $contact['HouseholdContact']);
		$this->Notifier->notify(
			array(
				'to' => $user,
				'template' => 'households_join'
			),
			'notification'
		);

		$this->redirect(array(
			'action' => 'index',
			'User' => $viewUser
		));
	}

/**
 * Removes/adds a user from/to a houshold
 *
 * Checks to see if the user is already in the household. If they are,
 * it removes them. If not, it will add them.
 *
 * @param integer $user The id of the user who is leaving
 * @param integer $household The id of the household the user is leaving
 */ 
	function shift_households($user, $household) {
		$viewUser = $this->passedArgs['User'];
		
		// check to see if they are in this household
		$householdMember = $this->Household->HouseholdMember->find('first', array(
			'conditions' => array(
				'household_id' => $household,
				'user_id' => $user
			)
		));
				
		if (empty($householdMember)) {			
			// add them to the household if it exists
			$this->Household->id = $household;
			if ($this->Household->exists($household)) {
				$addUser = $this->Household->HouseholdMember->User->find('first', array(
					'conditions' => array(	
						'User.id' => $user
					),
					'contain' => 'Profile'
				));
				$this->Household->HouseholdContact->contain(array('Profile'));
				$this->set('notifier', $this->Household->HouseholdContact->read(null, $this->activeUser['User']['id']));
				$this->Household->contain(array('HouseholdContact' => array('Profile')));
				$contact = $this->Household->read(null, $household);
				$this->set('contact', $contact['HouseholdContact']);
				
				$success = $this->Household->join(
					$household,
					$user,
					$this->activeUser['User']['id'],
					$addUser['Profile']['child']
				);
				
				if ($addUser['Profile']['child'] && $success) {
					$this->Notifier->notify(
						array(
							'to' => $user,
							'template' => 'households_join'
						),
						'notification'
					);
					$this->Session->setFlash('Added that dude.', 'flash'.DS.'success');
				} elseif (!$addUser['Profile']['child'] && $success) {
					$this->Notifier->notify(
						array(
							'to' => $user,
							'template' => 'households_invite',
							'type' => 'invitation'
						),
						'notification'
					);
					$this->Session->setFlash('Invited that dude.', 'flash'.DS.'success');
				} else {
					$this->Session->setFlash('Error joining household!', 'flash'.DS.'failure');
				}
			} else {
				$this->Session->setFlash('Invalid Id.');
			}
		} else {		
			// remove household member record
			$dSuccess = $this->Household->HouseholdMember->delete($householdMember['HouseholdMember']['id']);
			
			// add user to a household (function will check if they have one or not)
			$cSuccess = $this->Household->createHousehold($user);
			
			if ($dSuccess && $cSuccess) {
				$this->Household->contain(array('HouseholdContact' => array('Profile')));
				$contact = $this->Household->read(null, $household);
				$this->set('contact', $contact['HouseholdContact']);
				$this->Notifier->notify(
					array(
						'to' => $user,
						'template' => 'households_remove',
						'type' => 'invitation'
					),
					'notification'
				);

				$this->Session->setFlash('He left in a hurry.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Something broke. FIX IT!', 'flash'.DS.'failure');				
			}
		}
		
		$this->redirect(array(
			'action' => 'index',
			'User' => $viewUser
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