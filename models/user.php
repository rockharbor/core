<?php
/**
 * User model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * User model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class User extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'User';

/**
 * The field to use when generating lists
 *
 * @var string
 */
	var $displayField = 'username';

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	var $actsAs = array(
		'Containable',
		'Linkable.AdvancedLinkable',
		'Search.Searchable',
		'Logable'
	);

/**
 * Validation rules
 *
 * @var array
 */
	var $validate = array(
		'username' => array(
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'That username is unavailable.'
			),
			'notempty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please fill in the required field.'
			),
			'characters' => array(
				'rule' => '/^[a-z0-9\-_]/i',
				'message' => 'Username must only contain letters, numbers, dashes and underscores.',
				'allowEmpty' => true
			)
		),
		'password' => array(			
			'minLength' => array(
				'rule' => array('minLength', 6),
				'message' => 'Your password must be at least 6 characters long.'
			),
			'notempty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please fill in the required field.'
			)
		),
		'confirm_password' => array(
			'identical' => array(
				'rule' => array('identicalFieldValues', 'password'),
				'message' => 'Password confirmation does not match password.'
			)
		)
	);

/**
 * HasMany association link
 *
 * @var array
 */
	var $hasMany = array(
		'Comment' => array(
			'className' => 'Comment',
			'foreignKey' => 'user_id',
			'dependent' => true
		),
		'Notification' => array(
			'className' => 'Notification',
			'foreignKey' => 'user_id',
			'dependent' => true
		),
		'Image' => array(
			'className' => 'Image',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array('Image.model' => 'User', 'Image.group' => 'Image')
		),
		'Document' => array(
			'className' => 'Document',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array('Document.model' => 'User', 'Document.group' => 'Document')
		),
		'Roster' => array(
			'className' => 'Roster',
			'foreignKey' => 'user_id',
			'dependent' => true
		),
		'Address' => array(
			'className' => 'Address',
			'foreignKey' => 'foreign_key',
			'unique' => true,
			'conditions' => array('Address.model' => 'User'),
			'dependent' => true
		),
		'HouseholdMember' => array(
			'dependent' => true
		),
		'Leader' => array(
			'dependent' => true
		),
		'Payment' => array(
			'dependent' => true
		),
		'Invitation' => array(
			'dependent' => true
		)
	);

/**
 * HasOne association link
 *
 * @var array
 */
	var $hasOne = array(
		'Profile' => array(
			'className' => 'Profile',
			'foreignKey' => 'user_id',
			'dependent' => true
		),
		'ActiveAddress' => array(
			'className' => 'Address',
			'foreignKey' => 'foreign_key',
			'conditions' => array('ActiveAddress.model' => 'User', 'ActiveAddress.primary' => true),
			'limit' => 1
		)
	);

/**
 * BelongsTo association link
 *
 * @var array
 */
	var $belongsTo = array(
		'Group'
	);

/**
 * HasAndBelongsToMany association link
 *
 * @var array
 */
	var $hasAndBelongsToMany = array(
		'Publication' => array(
			'className' => 'Publication',
			'joinTable' => 'publications_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'publication_id',
			'dependent' => true
		)
	);

/**
 * Array of search filters for SearchesController::simple().
 * 
 * They are merged with any existing conditions and parameters sent to
 * Controller::paginate(). Works in conjunction with
 * SearchesController::simple() where arguments sent after the filter name are
 * inserted in order within the filter. Make sure to include contains or links
 * where related model data is needed.
 *
 * @var array
 */
	var $searchFilter = array(
		'notInHousehold' => array(
			'conditions' => array(
				'NOT EXISTS (SELECT 1 FROM household_members WHERE household_members.household_id = :0:
				AND household_members.user_id = User.id)'
			),
			'link' => array(
				'Profile',
				'ActiveAddress' => array(
					'fields' => array('city')
				)
			),
			'order' => 'first_name ASC, last_name ASC'
		),
		'notLeaderOf' => array(
			'conditions' => array(
				'Profile.qualified_leader' => true,
				'NOT EXISTS (SELECT 1 FROM leaders WHERE leaders.model = ":0:"
				AND leaders.model_id = :1: AND leaders.user_id = User.id)'
			),
			'link' => array(
				'Profile',
				'ActiveAddress' => array(
					'fields' => array('city')
				)
			),
			'order' => 'first_name ASC, last_name ASC'
		),
		'notSignedUp' => array(
			'conditions' => array(
				'NOT EXISTS (SELECT 1 FROM rosters WHERE rosters.involvement_id = :0:
				AND rosters.user_id = User.id)'
			),
			'link' => array(
				'Profile',
				'ActiveAddress' => array(
					'fields' => array('city')
				)
			),
			'order' => 'first_name ASC, last_name ASC'
		)
	);

/**
 * Filter args for the Search.Searchable behavior
 *
 * @var array
 * @see Search.Searchable::parseCriteria()
 */
	var $filterArgs = array(
		array(
			'name' => 'simple',
			'type' => 'query',
			'method' => 'makeLikeConditions',
			'field' => array(
				'User.username',
			)
		)
	);

/**
 * Checks to see if an image was included in the find. If so, and one is not
 * found, it will automatically pull the default image
 *
 * @param array $results The results
 * @param boolean $primary Association query?
 * @return array The modified results
 */
	function afterFind($results, $primary) {
		if ($primary) {
			foreach ($results as &$result) {
				$result = $this->defaultImage($result);
			}
		}
		return $results;
	}
	
/**
 * Merges two records and deletes the newer one
 * 
 * @param integer $mergeId The original account, to be updated
 * @param integer $modelId The new information to use to update
 * @return boolean Success
 */
	function merge($mergeId = null, $modelId = null) {
		if (empty($mergeId) || empty($modelId)) {
			return false;
		}
		$currentUser = $this->find('first', array(
			'conditions' => array(
				'User.id' => $mergeId
			),
			'contain' => array(
				'Profile',
				'Address'
			)
		));
		$updatedUser = $this->find('first', array(
			'conditions' => array(
				'User.id' => $modelId
			),
			'contain' => array(
				'Profile',
				'Address',
				'HouseholdMember',
				'Publication'
			)
		));
		
		// keep original fks
		unset($updatedUser['User']['id']);
		unset($updatedUser['Profile']['id']);
		unset($updatedUser['Profile']['user_id']);
		
		// merge data
		$user = $currentUser['User'];
		foreach ($updatedUser['User'] as $update => $val) {
			if ($val === null) {
				continue;
			}
			$user[$update] = $val;
		}
		$profile = $currentUser['Profile'];
		foreach ($updatedUser['Profile'] as $update => $val) {
			if ($val === null) {
				continue;
			}
			$profile[$update] = $val;
		}
		
		// activate user
		$user['active'] = true;
		
		$successes = array();
		$successes[] = $this->save($user, array('validate' => false));
		if (!empty($updatedUser['Publication'])) {
			$successes[] = $this->save(array(
				'Publication' => $updatedUser['Publication'],
				'User' => array(
					'id' => $user['id']
				)
			));
		}
		$successes[] = $this->Profile->save($profile, array('validate' => false));
		
		foreach ($updatedUser['Address'] as $address) {
			$address['foreign_key'] = $user['id'];
			$successes[] = $this->Address->save($address, array('validate' => false));
		}
		
		// make new address current
		if (!empty($updatedUser['Address']) && !empty($updatedUser['Address'][0]['address_line_1'])) {
			$this->Address->setPrimary($updatedUser['Address'][0]['id']);
		}
		
		// merge households
		$households = $this->HouseholdMember->Household->getHouseholdIds($user['id'], true);
		if (empty($households)) {
			$this->HouseholdMember->Household->createHousehold($user['id']);
			$households = $this->HouseholdMember->Household->getHouseholdIds($user['id'], true);
		}
		$oldHouseholdId = $household = $updatedUser['HouseholdMember'][0]['household_id'];
		
		if (count($households) == 1) {
			// if the user has 1 household, merge the households, otherwise the "new" household will be copied over
			$household = $households[0];
			// remove extra user
			$this->HouseholdMember->delete($updatedUser['HouseholdMember'][0]['id']);
		} else {
			// change to new fk
			$successes[] = $this->HouseholdMember->Household->save(array(
				'id' => $oldHouseholdId,
				'contact_id' => $mergeId
			));
			$successes[] = $this->HouseholdMember->save(array(
				'id' => $updatedUser['HouseholdMember'][0]['id'],
				'user_id' => $mergeId
			));
		}
		
		// move other members to new household
		$successes[] = $this->HouseholdMember->updateAll(
			array('household_id' => $household),
			array('household_id' => $oldHouseholdId)
		);
		
		$success = !in_array(false, $successes);
		if ($success) {
			$this->delete($modelId);
		}
		return $success;
	}

/**
 * Gets a list of users that match the data provided, using distinguishable 
 * fields (username, email fields, name, etc.). Returns a list of matching user 
 * ids. Uses `User::prepareSearch()` to generate the search options from the 
 * data.
 * 
 * @param array $data Data to search
 * @param string $operator The search operator
 * @return array The matching ids or an empty array
 * @see User::prepareSearch()
 */
	function findUser($data = array(), $operator = 'AND') {
		if (!is_array($data)) {
			$data = array($data);
		}
		
		if (empty($data)) {
			return array();
		}
		
		// normalize
		if (isset($data['User']) && isset($data['User']['Profile'])) {
			$data['Profile'] = $data['User']['Profile'];
			unset($data['User']['Profile']);
		}
		
		$_default = array(
			'User' => array(
				'username' => null,
			),
			'Profile' => array(
				'first_name' => null,
				'last_name' => null,
				'primary_email' => null,
				'birth_date' =>  null
			)
		);
		$data = Set::merge($_default, $data);

		$data = array(
			'Search' => array(
				'operator' => $operator
			),
			'User' => array(
				'username' => $data['User']['username']
			),
			'Profile' => array(
				'email' => $data['Profile']['primary_email'],
				'birth_date' =>  $data['Profile']['birth_date'],
				'first_name' => $data['Profile']['first_name'],
				'last_name' => $data['Profile']['last_name']
			)
		);
		
		$options = $this->prepareSearch(new AppController(), $data);
		$options['fields'] = 'User.id';

		if (empty($options['conditions'])) {
			// don't return all the users
			return array();
		}
		
		// special condition: since we don't want to search for "first_name" OR
		// "last_name", make them an AND condition
		if (strtolower($operator) == 'or') {
			if (isset($options['conditions'][$operator]['Profile.first_name LIKE'])) {
				$options['conditions'][$operator]['and']['Profile.first_name LIKE'] = $options['conditions'][$operator]['Profile.first_name LIKE'];
				unset($options['conditions'][$operator]['Profile.first_name LIKE']);
			}
			if (isset($options['conditions'][$operator]['Profile.last_name LIKE'])) {
				$options['conditions'][$operator]['and']['Profile.last_name LIKE'] = $options['conditions'][$operator]['Profile.last_name LIKE'];
				unset($options['conditions'][$operator]['Profile.last_name LIKE']);
			}
		}

		$foundUsers = $this->find('all', $options);
		
		return Set::extract('/User/id', $foundUsers);
	}

/**
 * Creates a User and adds them to a household, or creates a household for them
 *
 * @param array $data Data and related data to save
 * @param integer $householdId The id of the household for them to join. `null` creates a household for them
 * @param array $creator The person creating the user. Empty for self.
 * @param string $validate The value for the `validate` key in `saveAll()`. User model will *always* be validated
 * @return boolean Success
 */
	function createUser(&$data = array(), $householdId = null, $creator = array(), $validate = 'first') {
		if (!isset($this->tmpAdded)) {
			$this->tmpAdded = array();
		}
		if (!isset($this->tmpInvited)) {
			$this->tmpInvited = array();
		}

		// add missing data for the main user
		$data = $this->_createUserData($data);
		
		// temporarily remove household member info - we have to do that separately
		$householdMembers = isset($data['HouseholdMember']) ? $data['HouseholdMember'] : array();
		unset($data['HouseholdMember']);
		// validate new household members first
		$return = true;
		foreach ($householdMembers as $number => &$member) {
			$contain = array(
				'Profile' => array(
					'fields' => array(
						'id',
						'first_name',
						'last_name'
					)
				),
				'ActiveAddress' => array(
					'fields' => array(
						'city'
					)
				)
			);
			
			if (!empty($member['User']['id'])) {
				$this->contain($contain);
				$member = $this->read(array('id'), $member['User']['id']);
			} else {
				$foundUser = $this->findUser($member);
				
				if (count($foundUser) == 1) {
					// don't need to make them pick
					$this->contain($contain);
					$member = $this->read(array('id'), $foundUser[0]);
				} elseif (count($foundUser) > 0) {
					// user will have to pick one
					$this->contain($contain);
					$member['found'] = $this->find('all', array(
						'fields' => array(
							'id'
						),
						'conditions' => array(
							'User.id' => $foundUser
						)
					));
					$return = false;
				} else {
					// create a new user
					$allEmpty = Set::filter($member['Profile']);
					if (!empty($allEmpty)) {
						$member = $this->_createUserData($member);
						$this->create();
						if (!$this->saveAll($member, array('validate' => 'only'))) {
							$this->HouseholdMember->validationErrors += array(
								$number => $this->invalidFields()
							);
							$return = false;
						}
					}
				}
			}
		}
		
		if (!$return) {
			unset($data['User']['password']);
			unset($data['User']['confirm_password']);
			$data['HouseholdMember'] = $householdMembers;
			return false;
		}

		// save user and related info
		$this->create();
		$this->set($data['User']);
		$userValidates = $this->validates();
		$_errors = $this->validationErrors;
		$this->create();
		$this->validationErrors = $_errors;
		if ($userValidates && $this->saveAll($data, array('validate' => $validate))) {
			// needed for creating household members
			$data['User']['id'] = $this->id;

			if (empty($creator)) {
				$this->Profile->saveField('created_by', $this->id);
				$this->Profile->saveField('created_by_type', $data['User']['group_id']);
			}

			// temporarily store userdata for the controller to access and notify them
			$this->tmpAdded[] = array(
				'id' => $data['User']['id'],
				'username' => $data['User']['username'],
				'password' => $data['User']['password']
			);

			if (!$householdId) {
				// create a household for this user and add any members they wanted to add
				$this->HouseholdMember->Household->createHousehold($this->id);
				$householdId = $this->HouseholdMember->Household->id;
				$creator = $data;
			} elseif (!$this->HouseholdMember->Household->isMember($this->id, $householdId)) {
				$this->HouseholdMember->Household->join($householdId, $this->id, true);
			}

			foreach ($householdMembers as $householdMember) {
				if (!isset($householdMember['User']['id'])) {
					$householdMember['Profile']['created_by'] = $creator['User']['id'];
					$householdMember['Profile']['created_by_type'] = $creator['User']['group_id'];

					$this->create();
					if ($this->saveAll($householdMember, array('validate' => $validate))) {
						$this->HouseholdMember->Household->join($householdId, $this->id, true);
						$this->tmpAdded[] = array(
							'id' => $this->id,
							'username' => $householdMember['User']['username'],
							'password' => $householdMember['User']['password']
						);
					}
				} else {
					$this->HouseholdMember->Household->join($householdId, $householdMember['User']['id']);
					$this->contain(array('Profile'));
					$oldUser = $this->read(null, $householdMember['User']['id']);
					$this->tmpInvited[] = array(
						'id' => $oldUser['User']['id'],
						'username' => $oldUser['User']['username'],
						'password' => $oldUser['User']['password']
					);
				}
			}
			$this->id = $data['User']['id'];
			return true;
		} else {
			// add household member info back in to fill in fields if it failed
			$data['HouseholdMember'] = $householdMembers;
			unset($data['User']['password']);
			unset($data['User']['confirm_password']);

			return false;
		}
	}

/**
 * Merges current user data with basic needed data. Generates usernames and
 * passwords if empty.
 *
 * @param array $data The partial user data
 * @return array
 */
	function _createUserData($data = array()) {
		$userGroup = $this->Group->findByName('User');

		$default = array(
			'User' => array(
				'username' => null,
				'password' => null,
				'group_id' => $userGroup['Group']['id'],
				'active' => true
			),
			'Address' => array(
					0 => array(
						'primary' => true,
						'active' => true,
						'model' => 'User'
					)
			),
			'Profile' => array(
				'created_by_type' => 0,
				'created_by' => 0
			),
		);

		$data = Set::merge($default, $data);
		
		if (empty($data['Address'][0]['zip'])) {
			unset($data['Address']);
		}

		if (!$data['User']['username']) {
			$data['User']['username'] = $this->generateUsername($data['Profile']['first_name'], $data['Profile']['last_name']);
		}
		if (!$data['User']['password']) {
			$data['User']['password'] = $this->generatePassword();
			$data['User']['confirm_password'] = $data['User']['password'];
			$data['User']['reset_password'] = true;
		}

		return $data;
	}

/**
 * Prepares a search on a user
 *
 * Returns search options unique to this model that will return a list of id's from post conditions
 * that can then be used to search paginate data
 *
 * @param object $Controller The calling controller
 * @param array $data Post data to use for conditions
 * @return array Search option array
 * @access public
 */ 
	function prepareSearch(&$Controller, $data) {
		$_search = array(
			'Search' => array(
				'operator' => 'AND'
			),
			'Profile' => array(
				'Birthday' => array(),
				'email' => array()
			),
			'Distance' => array()
		);
		$data = Set::merge($_search, $data);

		// remove and store fields that aren't actually in the db
		$operator = $data['Search']['operator'];
		unset($data['Search']);
		$dist = $data['Distance'];
		unset($data['Distance']);
		$birthdayRange = $data['Profile']['Birthday'];
		$birthdayRange = array_map('Set::filter', $birthdayRange);
		unset($data['Profile']['Birthday']);
		$email = $data['Profile']['email'];
		unset($data['Profile']['email']);
		unset($data['User']['password']);
		unset($data['User']['confirm_password']);

		// remove blank
		$callback = function(&$item) use (&$callback) {
			if (is_array($item)) {
				$item = array_filter($item, $callback);
				return !empty($item);
			}
			if (!empty($item)) {
				return $item;
			}
		};
		$data = array_filter($data, $callback);
		$options = (array)$this->postOptions($data) + array('contain' => array());
		$link = $options['contain'];
		
		$conditions = $Controller->postConditions($data, 'LIKE', $operator);
		// prepare for a distance search
		if (!empty($dist['distance_from'])) {
			$coords = $this->Address->geoCoordinates($dist['distance_from']);
			$this->Address->virtualFields = array_merge($this->Address->virtualFields, array(
				'distance' => $this->Address->distance($coords['lat'], $coords['lng'])
			));
			
			// get addresses within distance requirements
			$distancedAddresses = $this->Address->find('all', array(
				'conditions' => array(
					$this->Address->getVirtualField('distance').' <= ' => (int)$dist['distance']
				)
			));
			$link['Address'] = array();
			$conditions[$operator]['Address.id'] = array_values(Set::extract('/Address/id', $distancedAddresses));
		}
		
		// prepare age group search
		if (isset($data['Profile']['age'])) {
			$ages = array();
			foreach ($data['Profile']['age'] as $ageGroup) {			
				$ageRange = explode('-', $ageGroup);
				$ages['or'][] = array($this->Profile->getVirtualField('age').' BETWEEN ? AND ?' => array((int)$ageRange[0], (int)$ageRange[1]));
			}
			$conditions[$operator][] = $ages;
			unset($conditions['Profile.age']);
			unset($conditions[$operator]['Profile.age']);
		}
		
		// check for child
		if (isset($data['Profile']['child'])) {
			$conditions[$operator][$this->Profile->getVirtualField('child')] = $data['Profile']['child'];
			unset($conditions['Profile.child']);
		}
		
		// check for birthday range
		if (!empty($birthdayRange['start']) && !empty($birthdayRange['end'])) {
			krsort($birthdayRange['start']);
			krsort($birthdayRange['end']);
			$start = implode('-', $birthdayRange['start']);
			$end = implode('-', $birthdayRange['end']);
			$conditions['Profile.birth_date BETWEEN ? AND ?'] = array($start, $end);
		}
		
		// check for birthdate
		if (!empty($data['Profile']['birth_date'])) {
			if (is_array($data['Profile']['birth_date'])) {
				$birthdate = $data['Profile']['birth_date'];
				$bday = $data['Profile']['birth_date']['year'].'-'.$data['Profile']['birth_date']['month'].'-'.$data['Profile']['birth_date']['day'];
			} else {
				$bday = date('Y-m-d', strtotime($data['Profile']['birth_date']));
			}
			// remove automatic conditions
			unset($conditions['Profile.birth_date']);
			unset($conditions['Profile.birth_date LIKE']);
			$conditions['Profile.birth_date'] = $bday;
		}
		
		// check for region
		if (!empty($data['Address']['Zipcode']['region_id'])) {
			$conditions[$operator]['Zipcode.region_id'] = $data['Address']['Zipcode']['region_id'];
			$link['Address']['Zipcode'] = array();
			unset($conditions['Address.Zipcode']);
		}
		
		// check for email 
		if (!empty($email)) {
			$conditions[$operator][] = array(
				'or' => array(
					'Profile.primary_email LIKE' => '%'.$email.'%',
					'Profile.alternate_email_1 LIKE' => '%'.$email.'%',
					'Profile.alternate_email_2 LIKE' => '%'.$email.'%'
				)
			);
			$link['Profile'] = array();
		}

		// check for involvement
		if (!empty($data['Roster']['Involvement']['name'])) {
			$conditions[$operator]['Involvement.name LIKE'] = '%'.$data['Roster']['Involvement']['name'].'%';
			unset($conditions['Roster.Involvement']);
		}
		
		// check for ministry
		if (!empty($data['Roster']['Involvement']['Ministry']['name'])) {
			$conditions[$operator]['Ministry.name LIKE'] = '%'.$data['Roster']['Involvement']['Ministry']['name'].'%';
			unset($conditions['Roster.Involvement.Ministry']);
		}
		
		if (strtolower($operator) == 'and' && isset($conditions[$operator])) {
			$conditions = array_merge($conditions, $conditions[$operator]);
			unset($conditions[$operator]);
		}
		
		if (strtolower($operator) == 'or' && empty($conditions[$operator])) {
			$conditions = array();
		}
		
		$group = 'User.id';
		
		return compact('link', 'conditions', 'group');
	}

	
/**
 * Generates a username from a name
 *
 * By default, it's $first_name.$last_name (without numbers). If that's taken, it
 * will continue appending numbers until it finds a unique username (up to 8 times).
 *
 * @param string $first_name User's first name
 * @param string $last_name User's last name
 * @return string Generated username
 * @todo Use a while loop instead so it works more than 8 digits
 */
	function generateUsername($first_name = '', $last_name = '') {
		$this->recursive = -1;

		if (empty($first_name) && empty($last_name)) {
			return '';
		}

		$username = strtolower($first_name.$last_name);
		$username = preg_replace('/[^a-z]/', '', $username);

		$user = $this->findByUsername($username);
		if (!empty($user)) {
			for ($x=1; $x <= 8; $x++) {
				$username .= rand(0,9);
				$user = $this->findByUsername($username);
				if (empty($user)) {
					break;
				}
			}
		}
		
		return $username;
	}
	
/**
 * Generates a random password
 *
 * Passwords are generated from a selection of random nouns and verbs,
 * and a 4-digit number is appended to the end. Characters that are 
 * difficult to discern (like '0', 'l', etc.) are replaced. Some other
 * characters are also replaced at random (like 4 for an 'a').
 *
 * @return string Generated password
 * @todo Add more nouns and verbs
 */
	function generatePassword() {
		$nouns = array('jesus', 'core', 'rockharbor', 'php', 'cake', 'pie');
		// 'is' is added in the middle		
		$verbs = array('awesome', 'swell', 'hilarious', 'thebest', 'socool');
		
		$noun = $nouns[array_rand($nouns, 1)];
		$verb = $verbs[array_rand($verbs, 1)];
		
		$rand_noun = '';
		$rand_verb = '';
		
		// shuffle the case around
		for($i = 0; $i < strlen($noun); $i++) {
			$rand_noun .= rand(0,1) ? strtoupper(substr($noun, $i, 1)) : strtolower(substr($noun, $i, 1));
		}
		for($i = 0; $i < strlen($verb); $i++) {
			$rand_verb .= rand(0,1) ? strtoupper(substr($verb, $i, 1)) : strtolower(substr($verb, $i, 1));			
		}
		
		$word = $rand_noun.'is'.$rand_verb;
		$rand_word = '';		
		// replace some letters that may be confusing, or for fun
		for($i = 0; $i < strlen($word); $i++) {
			$char = substr($word, $i, 1);
			if (in_array($char, array('a','A'))) {
				$rand_word .= rand(0,1) ? '4' : $char;
			} else if (in_array($char, array('e','E'))) {
				$rand_word .= rand(0,1) ? '3' : $char;
			} else if ($char == 'I') {
				// I and l's are confusing
				$rand_word .= rand(0,1) ? '1' : 'i';
			} else if ($char == 'l') {
				// I and l's are confusing
				$rand_word .= rand(0,1) ? '7' : 'L';			
			} else if (in_array($char, array('0', 'O'))) {
				// 0 and O's are confusing
				$rand_word .= 'o';
			} else {
				$rand_word .= $char;
			}
		}
		
		// append some numbers
		$num = '';
		for ($i = 0; $i < 4; $i++) {
			$num .= rand(2,9);
		}
		
		return $rand_word.$num;
	}
	
/**
 * Model::beforeSave() callback
 *
 * Used to hash the password field
 *
 * @return boolean True, to save
 * @see Cake docs
 */ 
	function beforeSave() {
		$this->hashPasswords(null, true);
		
		return parent::beforeSave();
	}
	

/**
 * Custom password hashing function for Auth component
 *
 * Hashes if $enforce is true. Prevents hashing before validation so that
 * password and confirm_password validation works and it doesn't require the
 * user to re-enter their password if it doesn't validate. Before the data is
 * saved it is automatically hashed.
 *
 * @param array $data Data passed. Uses User::data if it exists.
 * @param boolean $enforce Force hashing. Used to prevent Auth component from auto-hashing before validation
 * @return array Data with hashed passwords
 * @see User::beforeSave()
 */	
	function hashPasswords($data, $enforce = false) {
		App::import('Component', 'Auth');
		$Auth = new AuthComponent();

		if (!isset($data[$this->alias]['confirm_password'])) {
			// if confirm_password isn't sent, it's probably being sent by auth
			$enforce = true;
		}
		
		if (!empty($this->data)) {
			$data = $this->data;
		}
		
		if ($enforce && isset($data[$this->alias]['password']) && !empty($data[$this->alias]['password']))  {
			$data[$this->alias]['password'] = $Auth->password($data[$this->alias]['password']);
		}
		
		$this->data = $data;
		return $data;
	}
	
/**
 * Encrypts a string (used in old CORE's password authentication)
 * Uses Security.encryptKey in Configure
 *
 * @param string $str String to encrypt
 * @return string Encrypted string
 */	
	function encrypt($str) {
		if (empty($str)) {
			return '';
		}
	   $td = mcrypt_module_open('tripledes', '', 'ecb', '');
		$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);	    
		mcrypt_generic_init($td, Configure::read('Security.encryptKey'), $iv);
		$encrypted_data = mcrypt_generic($td, $str);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		    
		return $encrypted_data;
	}
	
/**
 * Decrypts a string (used in old CORE's password authentication)
 * Uses Security.encryptKey in Configure
 *
 * @param string $str String to decrypt
 * @return string Decrypted string
 */	
	function decrypt($str) {
		if (empty($str)) {
			return '';
		}
		$td = mcrypt_module_open('tripledes', '', 'ecb', '');
		$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		mcrypt_generic_init($td, Configure::read('Security.encryptKey'), $iv);
		$unencrypted_data = mdecrypt_generic($td, $str);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
					
		return trim($unencrypted_data);
	}
}
?>