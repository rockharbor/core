<?php
/**
 * Household model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Household model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Household extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Household';

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	var $actsAs = array(
		'Containable'
	);

/**
 * BelongsTo association link
 *
 * @var array
 */
	var $belongsTo = array(
		'HouseholdContact' => array(
			'className' => 'User',
			'foreignKey' => 'contact_id'
		)
	);

	var $hasMany = array(
		'HouseholdMember'
	);

/**
 * Checks if a user is a member of a household
 *
 * @param integer $userId The user id
 * @param integer $householdId The household id
 * @return boolean
 * @access public
 */ 
	function isMember($userId, $householdId) {
		return $this->HouseholdMember->hasAny(array(
			'household_id' => $householdId,
			'user_id' => $userId
		));
	}

/**
 * Checks if a user is a member of a household with another user
 *
 * To restrict which household it checks, use the parameter $household. Otherwise
 * it will check all of the users' households
 *
 * @param integer $userId The user id
 * @param integer $memberId The user to check $userId's association with
 * @param mixed $household The household id(s) to restrict the search to. Can be an array
 * @return boolean
 * @access public
 */ 	
	function isMemberWith($userId, $memberId, $household = null) {
		$households = $this->HouseholdMember->find('all', array(
			'conditions' => array(
				'HouseholdMember.user_id' => $userId
			),
			'contain' => false
		));
		
		if (!$household) {
			$household = array();
		} elseif (!is_array($household)) {
			$household = array($household);
		}

		if (!empty($household)) {
			$household = array_intersect(Set::extract('/HouseholdMember/household_id', $households), $household);
		} else {
			$household = Set::extract('/HouseholdMember/household_id', $households);
		}
		
		$members = $this->HouseholdMember->find('all', array(
			'conditions' => array(
				'HouseholdMember.user_id' => $memberId,
				'HouseholdMember.household_id' => $household
			),
			'contain' => false
		));
		
		return !empty($members);
	}

/**
 * Checks if a user is the Household Contact for a household
 *
 * @param integer $userId The user id
 * @param integer $householdId The household id
 * @return boolean
 * @access public
 */ 	
	function isContact($userId, $householdId) {
		$this->id = $householdId;
		return $this->field('id') == $userId;
	}

/**
 * Checks if a user is the Household Contact for another user
 *
 * @param integer $contactId The Household Contact
 * @param integer $userId The user id
 * @return boolean
 * @access public
 */ 		
	function isContactFor($contactId, $userId) {
		// get households for the user
		$households = $this->HouseholdMember->find('all', array(
			'conditions' => array(
				'HouseholdMember.user_id' => $userId,
				'Household.contact_id' => $contactId
			),
			'link' => array(
				'Household' => array(
					'HouseholdContact'
				)
			)
		));
		
		return !empty($households);
	}

/**
 * Creates a household for a user
 *
 * Only creates a household if they don't currently belong to a household
 * (including their own)
 *
 * @param integer $user User id
 * @return boolean True on success, false on failure
 * @access public
 */ 
	function createHousehold($user) {
		if (!$this->HouseholdMember->hasAny(array('user_id' => $user))) {
			// create household			
			if (!$this->hasAny(array('contact_id' => $user))) {
				$this->create();
				$hSuccess = $this->save(array('contact_id' => $user));
			} else {
				$hSuccess = true;
				$this->id = $this->field('id', array('contact_id' => $user));
			}
			// add them to their household
			$hmSuccess = $this->HouseholdMember->save(array(
				'household_id' => $this->id,
				'user_id' => $user,
				'confirmed' => true
			));
			return $hSuccess && $hmSuccess;
		}
		
		return true;
	}
	
/**
 * Makes a user the household contact
 *
 * @param integer $user User id
 * @param integer $household Household id
 * @return boolean True on success, false on failure
 * @access public
 * @todo Should check and fail if they're a child, inactive, exist, confirmed etc.
 */ 	
	function makeHouseholdContact($user, $household) {
		$this->id = $household;
		if ($this->isMember($user, $household)) {
			return $this->saveField('contact_id', $user);
		} else {
			return false;
		}
	}
	
/**
 * Adds or invites a user to a household
 *
 * @param integer $household Household id
 * @param integer $user User id
 * @param integer $notifier The user who is adding/inviting
 * @param boolean $confirm True to add, false to invite
 * @return boolean True on success, false on failure
 * @access public
 * @todo remove notifier as it's unneeded
 */ 
	function join($household, $user, $notifier = null, $confirm = false) {
		$this->HouseholdMember->User->id = $user;
		$this->id = $household;
		// find the user
		if (!$this->HouseholdMember->User->exists($user) || !$this->exists($household)) {
			return false;
		}

		$member = $this->HouseholdMember->find('first', array(
			'conditions' => array(
				'household_id' => $household,
				'user_id' => $user
			)
		));
		
		if (!empty($member)) {
			// in the household already
			$this->HouseholdMember->id = $member['HouseholdMember']['id'];			
			$success = $this->HouseholdMember->saveField('confirmed', $confirm);
		} else {
			// not in the household
			$this->HouseholdMember->create();
			$success = $this->HouseholdMember->save(array(
				'household_id' => $household,
				'user_id' => $user,
				'confirmed' => $confirm
			));
		}
		
		return $success;
	}
}
?>