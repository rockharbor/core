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
			)
		));
		
		if (!$household) {
			$household = array();
		} elseif (!is_array($household)) {
			$household = array($household);
		}
		
		$household = array_intersect(Set::extract('/HouseholdMember/household_id', $households), $household);	
		
		$members = $this->HouseholdMember->find('all', array(
			'conditions' => array(
				'HouseholdMember.user_id' => $userId,
				'HouseholdMember.household_id' => $household
			)
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
 */ 	
	function makeHouseholdContact($user, $household) {
		$this->id = $household;
		return $this->saveField('contact_id', $user);		
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
 */ 
	function join($household, $user, $notifier, $confirm = false) {		
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

/**
 * Adds members to a household on behalf of a user. If the user doesn't exist
 * they will be created
 *
 * @param array $members The members to add including Profile data
 * @param integer $householdId The id of the Household to add them to
 * @param array $creator The user data for the creator
 * @return boolean Success
 */
	function addMembers($members = array(), $householdId = null, $creator = array()) {
		if (empty($members) || !$householdId || empty($creator)) {
			return false;
		}

		foreach ($members as &$member) {
			// check to see if the new household members exists and use that data instead
			$foundUser = $this->HouseholdMember->User->findUser(array(
				$member['primary_email'],
				$member['first_name'],
				$member['last_name']
			));

			if ($foundUser !== false) {
				$this->HouseholdMember->User->contain(array('Profile'));
				$hm = $this->HouseholdMember->User->read(null, $foundUser);
				$this->join(
					$householdId,
					$hm['User']['id'],
					$creator['User']['id'],
					$hm['Profile']['child']
				);

				if ($hm['Profile']['child']) {
					if (!isset($this->tmpInvites) || !is_array($this->tmpInvites)) {
						$this->tmpInvites = array();
					}

					// temporarily store userdata for the controller to access and notify them
					$this->tmpInvites[] = array(
						'user' => $hm['User']['id'],
						'household' => $householdId
					);
				}
			} elseif (!empty($member['first_name']) && !empty($member['last_name']) && !empty($member['primary_email'])) {
				// get creators address to use
				$address = $this->HouseholdMember->User->Address->find('first', array(
					'Address.foreign_key' => $creator['User']['id'],
					'Address.model' => 'User'
				));

				// remove unnecessary fields
				unset($address['Address']['id']);
				unset($address['Address']['foreign_key']);
				unset($address['Address']['primary']);
				unset($address['Address']['active']);

				// we need to create a new user
				$newHouseholdMember = array(
					'Profile' => array(
						'first_name' => $member['first_name'],
						'last_name' => $member['last_name'],
						'primary_email' => $member['primary_email']
					),
					'Address' => array(
							0 => $address['Address']
					)
				);

				$this->HouseholdMember->User->createUser($newHouseholdMember, $householdId, $creator);
			}
		}
		
		return true;
	}
}
?>