<?php
class UsersController extends AppController {

	var $name = 'Users';
	
	var $helpers = array('Formatting', 'SelectOptions', 'MultiSelect');
	
	var $components = array('FilterPagination', 'MultiSelect');

/**
 * Model::beforeFilter() callback
 *
 * Sets permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
		
		// public actions
		$this->Auth->allow('login', 'logout', 'forgot_password', 'add', 'request_activation');
		
		$this->_editSelf('edit', 'edit_profile');
	}
	
/**
 * Runs a search on simple fields (username, first_name, etc.)
 *
 * #### Params:
 * - Every named parameter is treated as an "action". Each action should have a key 
 * value pair. The key is the name to display, value is the js function to run (no parens).
 * The selected user id is always passed as the first param to the js function
 *
 * #### Filters: Everything passed as an argument are considered filters.
 * Filters are used to help pre-filter the results (i.e., don't show people 
 * who are in a specific household). Passed like filter:[filter] [Model].[field] [value]
 * Example: not HouseholdMember.household_id 12
 * - Filters of the same model are grouped together, i.e., is Leader.model ministry AND not Leader.model_id 1
 * would produce a condition (Leader.model="ministry" AND Leader.model_id<>1) ...
 * - "NOT" filters also produce a OR NULL condition so that users without records still show up
 */
	function simple_search() {
		$filters = func_get_args();
		$allowedFilters = array('in', 'not', 'is');
		
		$results = array();
		$searchRan = false;

		if (!empty($this->data)) {
			$conditions = array();
			// at the very least, we want profile too
			$contain = array('Profile'=>array());			
			// create conditions
			foreach ($this->data as $model => $fields) {
				foreach ($fields as $key => $value) {
					if ($value != '') {
						$conditions[$model.'.'.$key.' like'] = '%'.$value.'%';
						// use it in the find
						$contain[$model] = array();
					}
				}
			}
			
			// add filters
			$tables = array();	
			$joinConditions = array();
			$nullConditions = array();
			foreach ($filters as $filter) {				
				$filter = explode(' ', $filter);
				// get filter info
				list($filter, $modelField, $modelId) = $filter;
				$modelField = explode('.', $modelField);
				$model = $modelField[0];
				$field = $modelField[1];				
					
				if (in_array($filter, $allowedFilters)) {
					// workaround for now					
					if ($this->User->{$model}->isVirtualField($field)) {
						$conditionField = $this->User->{$model}->getVirtualField($field);
					} else {
						$conditionField = $model.'.'.$field;
					}
					
					// temp belongsTo (to join, use conditions, etc.)
					$hasOne = array(
						$model => array(
							'className' => Inflector::classify($model),
							'foreignKey' => 'user_id',
							'conditions' => array(
								$conditionField => $modelId
							)
						)
					);
					
					// merge, just in case it already exists
					$this->User->hasOne = Set::merge($this->User->hasOne, $hasOne);

					$contain[$model] = array();
					
					switch ($filter) {
						case 'in':
							$joinConditions[$model][$conditionField] = array($modelId);
						break;
						case 'is':
							$joinConditions[$model][$conditionField] = $modelId;
						break;
						case 'not':						
							// add the null condition for users without a record
							$joinConditions[$model][$conditionField.' <>'] = $modelId;
							$nullConditions[$model][$conditionField] = null;
						break;
					}
				}
			}

			foreach ($joinConditions as $modelVal => $modelConditions) {
				// combine model conditions with null conditions to prevent misleading
				// results due to lack of records (i.e., a user isn't leading)
				if (!empty($nullConditions[$modelVal])) {
					$conditions[] = array(
						'or' => array($joinConditions[$modelVal], $nullConditions[$modelVal])
					);
				} else {
					$conditions[] = $modelConditions;
				}
			}
			
			// User can't contain User!
			unset($contain['User']);
			$this->paginate = compact('conditions', 'contain');
			$searchRan = true;
		}
		
		$results = $this->FilterPagination->paginate();
		
		$this->set('filters', implode(',',$filters));
		// remove pagination info from action list
		$actions = array_diff_key($this->params['named'], array('page'=>array(),'sort'=>array(),'direction'=>array()));
		$this->set(compact('results','searchRan','actions'));
	}
	
/**
 * Logs a user into CORE, saves their profile data in session
 *
 * @param string $username Used to auto-fill the username field
 */
	function login($username = null) {		
		if (isset($this->passedArgs['message'])) {
			$this->Session->setFlash($this->passedArgs['message']);
		}
		
		if (!empty($this->data)) {		
			if ($this->Auth->login($this->data)) {
				$this->User->contain(array('Profile', 'Group'));
				$this->Session->write('User', $this->User->read(null, $this->Auth->user('id')));
				
				$this->User->id = $this->Auth->user('id');
				$this->User->saveField('last_logged_in', date('Y-m-d H:i:s'));
			
				// go!
				$this->redirect($this->Auth->redirect());
			} else {
				// trick into not redirecting and to highlighting fields
				$this->User->invalidate('username', 'Invalid'); 
				$this->User->invalidate('password', 'Invalid');
			}
		}
		
		if (empty($this->data) && $username) {
			$this->data['User']['username'] = $username;
		}
	}
	
/**
 * Logs a user out of CORE
 */	
	function logout() {
		$redirect = $this->Auth->logout();
		$this->Session->destroy();
		$this->redirect($redirect);
	}
	
	function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

	function view() {
		// get user id
		$id = $this->passedArgs['User'];
	
		if (!$id) {
			//$this->Session->setFlash(__('Invalid user', true));
			//$this->redirect(array('action' => 'index'));
		}
		
		$this->User->recursive = 2;
		$this->set('user', $this->User->read(null, $id));
	}

/**
 * Creates a new password and sends it to the user, or offers them to reset
 *
 * @param boolean $reset Whether the password was forgotten (send to email) or they are resetting
 */
	function edit() {
		$needCurrentPassword = $this->activeUser['User']['id'] == $this->passedArgs['User'];
		
		if (!empty($this->data)) {
			$invalidPassword = false;
			
			// check if they're resetting their username or password and stop validation for the other
			switch ($this->data['User']['reset']) {
				case 'username':
					unset($this->data['User']['password']);
					unset($this->data['User']['current_password']);
					unset($this->data['User']['confirm_password']);					
					$this->User->id = $this->data['User']['id'];
					// avoid needing a password to save
					$success = $this->User->saveField('username', $this->data['User']['username']);
					$this->set('username', $this->data['User']['username']);
					$subject = 'New username';
				break;
				case 'password':
					unset($this->data['User']['username']);
					if ($needCurrentPassword && $this->User->encrypt($this->data['User']['current_password']) != $this->User->field('password')) {
						$invalidPassword = true;
					}
					// avoid needing a username to save
					if ($this->User->validates(array('fieldList' => array('password', 'confirm_password')))) {
						$this->User->id = $this->data['User']['id'];
						$success = $this->User->saveField('password', $this->data['User']['password']);
					} else {
						$success = false;
					}
					$this->set('password', $this->data['User']['password']);
					$subject = 'New password';
				break;
				case 'both':
					$success = $this->User->save($this->data);
					$this->set('username', $this->data['User']['username']);
					$this->set('password', $this->data['User']['password']);
					$subject = 'New username and password';
				break;
			}
			
			if ($success) {
				$this->Session->setFlash('Please log in with your new credentials.', 'flash_success');
				$this->set('reset', $this->data['User']['reset']);
				$this->_sendEmail(array(
					'to' => $this->data['User']['id'],
					'subject' => $subject,
					'template' => 'users_edit'
				));
				$this->redirect(array('action' => 'logout'));
			} else {
				if ($invalidPassword) {
					$this->User->invalidate('current_password', 'What exactly are you trying to pull? This isn\'t your current password.');
				}
				$this->Session->setFlash('D\'oh! Couldn\'t reset password. Please, try again.', 'flash_failure');
			}
		}
		
		if (empty($this->data)) {
			$this->User->id = $this->passedArgs['User'];
			$this->User->contain(false);
			$this->data = $this->User->read();
		}
		
		$this->set('needCurrentPassword', $needCurrentPassword);
	}
	
	function forgot_password() {		
		if (!empty($this->data)) {
			if (!empty($this->data['User']['forgotten'])) {			
				$user = $this->User->find('first', array(
					'or' => array(
						'User.username' => $this->data['User']['forgotten'],
						'Profile.primary_email' => $this->data['User']['forgotten'],
						'Profile.alternate_email_1' => $this->data['User']['forgotten'],
						'Profile.alternate_email_2' => $this->data['User']['forgotten']
					)
				));
			} else {
				$user = array();
			}
			
			if (!empty($user)) {
				$this->User->id = $user['User']['id'];
		
				$newPassword = $this->User->generatePassword();
		
				if ($this->User->saveField('password', $newPassword)) {
					$this->Session->setFlash('Your new password has been sent via email.', 'flash_success');
					$this->set('password', $newPassword);
					$this->_sendEmail(array(
						'to' => $user['User']['id'],
						'subject' => 'Password reset',
						'template' => 'users_forgot_password'
					));
				} else {
					$this->Session->setFlash('D\'oh! Couldn\'t reset password. Please, try again.', 'flash_failure');
				}
			} else {
				$this->Session->setFlash('I couldn\'t find you. Try again.', 'flash_failure');
			}
		}
	}

/**
 * Creates a profile for the user and a merge request
 *
 * @param integer $foundId The id of the user to merge with
 * @param boolean $initialRedirect True if came directly from UsersController::add()
 */ 
	function request_activation($foundId, $initialRedirect = false) {		
		if (!empty($this->data) && !$initialRedirect && $foundId) {		
			$group = $this->User->Group->findByLevel(1);
			$publicId = $group['Group']['id'];
			
			$this->data['User']['active'] = false;	
			$this->data['Address'][0]['model'] = 'User';
			$this->data['Group'] = array($publicId);
			
			// remove isUnique validation for email and username
			unset($this->User->validate['username']['isUnique']);
			unset($this->User->Profile->validate['primary_email']['isUnique']);
			
			// create near-empty user for now (for merging)
			if ($this->User->saveAll($this->data, array('validate' => 'first'))) {
				// save merge request
				$MergeRequest = ClassRegistry::init('MergeRequest');
				$MergeRequest->save(array(
					'model' => 'User',
					'model_id' => $foundId,
					'merge_id' => $this->User->id,
					'requester_id' => $this->User->id
				));
				$this->Notifier->notify($this->CORE['settings']['activation_requests_user'], 'users_request_activation');
				$this->_sendEmail(array(
					'to' => $this->CORE['settings']['activation_requests_user'],
					'subject' => 'Account activation request',
					'template' => 'users_request_activation'
				));

				$this->Session->setFlash('Request sent!', 'flash_success');
				$this->redirect('/');
			} else {
				$this->Session->setFlash('Fill out all the info por favor.', 'flash_failure');
			}			
		}
		
		$this->set('foundId', $foundId);
	}
	
/**
 * Registers a user
 */
	function add() {	
		if (!empty($this->data)) {			
			// if staff is adding this user, auto-generate their username/password
			if ($this->Auth->user()) {
				$this->data['User']['username'] = $this->User->generateUsername($this->data['Profile']['first_name'], $this->data['Profile']['last_name']);
				$this->data['User']['password'] = $this->User->generatePassword();
			}
			
			// check if user exists
			$foundUser = $this->User->find('first', array(
				'fields' => 'User.id',
				'conditions' => array(
					'or' => array(
						'and' => array(
							'Profile.first_name' => $this->data['Profile']['first_name'],
							'Profile.last_name' => $this->data['Profile']['last_name']
						),
					'Profile.primary_email' => $this->data['Profile']['primary_email']
					)
				),
				'contain' => array(
					'Profile'
				)
			));
			
			if (!empty($foundUser)) {				
				// take to activation request (preserve data)
				return $this->setAction('request_activation', $foundUser['User']['id'], true);
			}
			
			// add group
			$this->data['User']['group_id'] = 9;
			
			// check to see if this user exists
			foreach ($this->data['HouseholdMember'] as &$householdMember) {				
				// check if user exists
				if ($this->User->Profile->hasAny(array(
					'Profile.primary_email' => $householdMember['primary_email']
				)) ||
				$this->User->Profile->hasAny(array(
					'Profile.first_name' => $householdMember['first_name'],
					'Profile.last_name' => $householdMember['last_name']
				))) {
					// user exists already
					$hm = $this->User->Profile->find('first', array(
						'conditions' => array(
							'or' => array(
								'and' => array(
									'Profile.first_name' => $householdMember['first_name'],
									'Profile.last_name' => $householdMember['last_name']
								),
								'Profile.primary_email' => $householdMember['primary_email']
							)
						),
						'contain' => 'User'
					));
					
					$householdMember = array_merge($householdMember, $hm);
				}
			}
			
			// temporarily remove household member info - we have to do that separately
			$householdMembers = $this->data['HouseholdMember'];
			unset($this->data['HouseholdMember']);
			
			// first, check validation
			if ($this->User->saveAll($this->data, array('validate' => 'only'))) {
				$creatorGroupId = $this->activeUser ? $this->activeUser['User']['group_id'] : 9;
				
				// extra data
				$this->data['Profile']['created_by_type'] = $creatorGroupId;
				$this->data['Address'][0]['primary'] = true;
				$this->data['Address'][0]['active'] = true;
				$this->data['Address'][0]['model'] = 'User';				
				
				// save user and related info
				$this->User->create();
				$this->User->saveAll($this->data, array('validate' => false));

				$newUserId = $this->User->id;
				
				if ($this->Auth->user()) {
					$this->User->Profile->saveField('created_by', $this->activeUser['User']['id']);
				} else {
					// save that they created themselves
					$this->User->Profile->saveField('created_by', $newUserId);
				}
				
				// create household (new user is automatically added to the new household)
				$this->User->HouseholdMember->Household->createHousehold($newUserId);
				
				foreach ($householdMembers as &$householdMember) {
					if (isset($householdMember['User'])) {						
						// join household
						$this->User->HouseholdMember->Household->join(
							$this->User->HouseholdMember->Household->id,
							$householdMember['User']['id'],
							$newUserId,
							$householdMember['Profile']['child']
						);
					} elseif (!empty($householdMember['first_name']) && !empty($householdMember['last_name']) && !empty($householdMember['primary_email'])) {
						// we need to create a new user
						$this->User->create();
						$this->User->saveAll(array(
							'User' => array(
								'username' => $this->User->generateUsername($householdMember['first_name'], $householdMember['last_name']),
								'password' => $this->User->generatePassword(),
								'active' => true
							),
							'Group' => array($publicId)
						));
						
						// .. and a profile to go with
						$this->User->Profile->create();
						$this->User->Profile->save(array(
							'user_id' => $this->User->id,
							'first_name' => $householdMember['first_name'],
							'last_name' => $householdMember['last_name'],
							'created_by' => $newUserId,
							'created_by_type' => $publicId
						));
						
						// join new user's household
						$this->User->HouseholdMember->Household->join(
							$this->User->HouseholdMember->Household->id,
							$this->User->id,
							$newUserId,
							true
						);
					}
				}
				
				if ($this->Auth->user()) {
					$this->Session->setFlash('User added!', 'flash_success');
					$this->set('username', $this->data['User']['username']);
					$this->set('password', $this->data['User']['password']);
					$this->_sendEmail(array(
						'to' => $newUserId,
						'subject' => 'User registration',
						'template' => 'users_register'
					));
					
					$this->redirect(array(
						'controller' => 'users',
						'action' => 'index'
					));
				} else {
					$this->Session->setFlash('Your account has been created!', 'flash_success');
					$this->set('username', $this->data['User']['username']);
					$this->set('password', $this->data['User']['password']);
					$this->_sendEmail(array(
						'to' => $newUserId,
						'subject' => 'User registration',
						'template' => 'users_register'
					));
								
					$this->redirect(array(
						'controller' => 'users',
						'action' => 'login',
						$this->data['User']['username']
					));
				}					
			} else {		
				$this->Session->setFlash('Oops, validation errors...', 'flash_failure');
			}
			
			// add household member info back in
			$this->data['HouseholdMember'] = $householdMembers;
		}
		
		$this->set('elementarySchools', $this->User->Profile->ElementarySchool->find('list', array(
			'conditions' => array('type' => 'e')
		)));
		$this->set('middleSchools', $this->User->Profile->MiddleSchool->find('list', array(
			'conditions' => array('type' => 'm')
		)));
		$this->set('highSchools', $this->User->Profile->HighSchool->find('list', array(
			'conditions' => array('type' => 'h')
		)));
		$this->set('colleges', $this->User->Profile->College->find('list', array(
			'conditions' => array('type' => 'c')
		)));
		$this->set('publications', $this->User->Publication->find('list')); 
		$this->set('jobCategories', $this->User->Profile->JobCategory->find('list')); 
		$this->set('classifications', $this->User->Profile->Classification->find('list'));
	}


/**
 * Edits a user. Includes their group and profile information
 */	
	function edit_profile() {
		// get user id
		if (isset($this->passedArgs['User'])) {
			$id = $this->passedArgs['User'];
		} else {
			$id = $this->activeUser['User']['id'];
		}
		
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Invalid user');
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->saveAll($this->data)) {
				$this->Session->setFlash('The user has been saved', 'flash_success');
			} else {
				$this->Session->setFlash('The user could not be saved. Please, try again.', 'flash_failure');
			}
		}
		if (empty($this->data)) {
			$this->User->contain(array(
				'Profile',
				'Group',
				'Publication'
			));
			$this->data = $this->User->read(null, $id);
		}
		
		$user = $this->User->find('first', array(
			'conditions' => array(
				'id' => $id
			),
			'contain' => false
		));
		
		$this->set('user', $user); 
		$this->set('publications', $this->User->Publication->find('list')); 
		$this->set('groups', $this->User->Group->find('list', array(
			'conditions' => array(
				'Group.conditional' => false,
				'Group.lft >' => $this->activeUser['Group']['lft']
			)
		))); 
		$this->set('campuses', $this->User->Profile->Campus->find('list')); 
		$this->set('jobCategories', $this->User->Profile->JobCategory->find('list'));
		$this->set('elementarySchools', $this->User->Profile->ElementarySchool->find('list', array(
			'conditions' => array('type' => 'e')
		)));
		$this->set('middleSchools', $this->User->Profile->MiddleSchool->find('list', array(
			'conditions' => array('type' => 'm')
		)));
		$this->set('highSchools', $this->User->Profile->HighSchool->find('list', array(
			'conditions' => array('type' => 'h')
		)));
		$this->set('colleges', $this->User->Profile->College->find('list', array(
			'conditions' => array('type' => 'c')
		)));
		$this->set('classifications', $this->User->Profile->Classification->find('list'));
	}

	function delete($id = null) {		
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for user', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->delete($id)) {
			$this->Session->setFlash(__('User deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('User was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>