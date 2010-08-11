<?php
/**
 * User controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Users Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 * @todo Split add into add and register, move simple search to searches
 */
class UsersController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Users';

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array('Formatting', 'SelectOptions', 'MultiSelect.MultiSelect');

/**
 * Extra components for this controller
 *
 * @var array
 */
	var $components = array('FilterPagination', 'MultiSelect.MultiSelect', 'Cookie');

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
		
		// public actions
		$this->Auth->allow('login', 'logout', 'forgot_password', 'register', 'request_activation');
		
		$this->_editSelf('edit', 'edit_profile');
	}
	
/**
 * Runs a search on simple fields (username, first_name, etc.)
 *
 * ### Params:
 * - Every named parameter is treated as an "action". Each action should have a key 
 * value pair. The key is the name to display, value is the js function to run (no parens).
 * The selected user id is always passed as the first param to the js function
 *
 * ### Filters: Everything passed as an argument are considered filters.
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
 * @todo Restrict login to users older than 12 (use Auth.userScope?)
 */
	function login($username = null) {		
		if (isset($this->passedArgs['message'])) {
			$this->Session->setFlash($this->passedArgs['message']);
		}
			
		// check for remember me checkbox
		if (!empty($this->data) && $this->data['User']['remember_me']) {
			unset($this->data['User']['remember_me']);
			$this->Session->del('Message.auth');
			$this->Cookie->write('Auth.User', $this->data['User'], false, '+2 weeks');
		}

		// check for remember me cookie and use that data
		if (empty($this->data) && !is_null($this->Cookie->read('Auth.User'))) {
			$this->data['User'] = $this->Cookie->read('Auth.User');
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
		$this->Cookie->delete('Auth');
		$this->Session->destroy();
		$this->redirect($redirect);
	}

/**
 * Lists users
 *
 * @todo Remove this altogether?
 */
	function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

/**
 * Shows user information
 */
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
				$this->QueueEmail->send(array(
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

/**
 * Sends a user a new password
 */
	function forgot_password() {		
		if (!empty($this->data)) {
			if (!empty($this->data['User']['forgotten'])) {			
				$user = $this->User->findUser(explode(' ', $this->data['User']['forgotten']));
			} else {
				$user = false;
			}
			
			if ($user !== false) {
				$this->User->id = $user;
		
				$newPassword = $this->User->generatePassword();
		
				if ($this->User->saveField('password', $newPassword)) {
					$this->Session->setFlash('Your new password has been sent via email.', 'flash_success');
					$this->set('password', $newPassword);
					$this->QueueEmail->send(array(
						'to' => $user,
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
			$group = $this->User->Group->findByName('User');
			
			$this->data['User']['active'] = false;	
			$this->data['Address'][0]['model'] = 'User';
			
			// remove isUnique validation for email and username
			unset($this->User->validate['username']['isUnique']);
			unset($this->User->Profile->validate['primary_email']['isUnique']);
			
			// create near-empty user for now (for merging)
			if ($this->User->createUser($this->data, null, $this->activeUser)) {
				// save merge request
				$MergeRequest = ClassRegistry::init('MergeRequest');
				$MergeRequest->save(array(
					'model' => 'User',
					'model_id' => $foundId,
					'merge_id' => $this->User->id,
					'requester_id' => $this->User->id
				));
				$this->Notifier->notify($this->CORE['settings']['activation_requests_user'], 'users_request_activation');
				$this->QueueEmail->send(array(
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
 * Adds a user
 */
	function add() {
		if (!empty($this->data)) {
			// check if user exists
			$foundUser = $this->User->findUser(array(
				$this->data['User']['username'],
				$this->data['Profile']['primary_email'],
				$this->data['Profile']['first_name'],
				$this->data['Profile']['last_name']
			));

			if ($foundUser !== false) {
				$this->Session->setFlash('User already exists!', 'flash_failure');
				$this->redirect(array('action' => 'view', 'User' => 1));
			}

			if ($this->User->createUser($this->data, null, $this->activeUser)) {
				foreach ($this->User->tmpAdded as $notifyUser) {
					$this->set('username', $notifyUser['username']);
					$this->set('password', $notifyUser['password']);
					$this->QueueEmail->send(array(
						'to' => $notifyUser['id'],
						'template' => 'users_register',
						'subject' => 'Account registration'
					));
					$this->Notifier->notify($notifyUser['id'], 'users_register');
				}

				foreach ($this->User->tmpInvited as $notifyUser) {
					$this->User->contain(array('Profile'));
					$this->set('notifier', $this->User->read(null, $this->activeUser['User']['id']));
					$this->set('contact', $this->User->read(null, $this->User->id));
					$this->Notifier->saveData = array('type' => 'invitation');
					$this->Notifier->notify($notifyUser['id'], 'households_invite');
				}

				$this->Session->setFlash('User(s) added and notified!', 'flash_success');

				$this->redirect(array(
					'controller' => 'users',
					'action' => 'index'
				));
			} else {		
				$this->Session->setFlash('Oops, validation errors...', 'flash_failure');
			}
		}
		
		$this->set('elementarySchools', $this->User->Profile->ElementarySchool->find('list'));
		$this->set('middleSchools', $this->User->Profile->MiddleSchool->find('list'));
		$this->set('highSchools', $this->User->Profile->HighSchool->find('list'));
		$this->set('colleges', $this->User->Profile->College->find('list'));
		$this->set('publications', $this->User->Publication->find('list')); 
		$this->set('jobCategories', $this->User->Profile->JobCategory->find('list')); 
		$this->set('classifications', $this->User->Profile->Classification->find('list'));
		$this->set('campuses', $this->User->Profile->Campus->find('list'));
	}

/**
 * Registers a user
 */
	function register() {
		if (!empty($this->data)) {
			// check if user exists
			$foundUser = $this->User->findUser(array(
				$this->data['User']['username'],
				$this->data['Profile']['primary_email'],
				$this->data['Profile']['first_name'],
				$this->data['Profile']['last_name']
			));

			if ($foundUser !== false) {
				// take to activation request (preserve data)
				return $this->setAction('request_activation', $foundUser, true);
			}
			
			if ($this->User->createUser($this->data)) {
				$this->Session->setFlash('Your account has been created!', 'flash_success');

				foreach ($this->User->tmpAdded as $notifyUser) {
					$this->set('username', $notifyUser['username']);
					$this->set('password', $notifyUser['password']);
					$sent = $this->QueueEmail->send(array(
						'to' => $notifyUser['id'],
						'template' => 'users_register',
						'subject' => 'Account registration'
					));
					$this->Notifier->notify($notifyUser['id'], 'users_register');
				}

				foreach ($this->User->tmpInvited as $notifyUser) {
					$this->User->contain(array('Profile'));
					$this->set('notifier', $this->User->read(null, $this->User->id));
					$this->set('contact', $this->User->read(null, $this->User->id));
					$this->Notifier->saveData = array('type' => 'invitation');
					$this->Notifier->notify($notifyUser['user'], 'households_invite');
				}

				$this->redirect(array(
					'controller' => 'users',
					'action' => 'login',
					$this->data['User']['username']
				));
			} else {
				$this->Session->setFlash('Oops, validation errors...', 'flash_failure');
			}
		}

		$this->set('elementarySchools', $this->User->Profile->ElementarySchool->find('list'));
		$this->set('middleSchools', $this->User->Profile->MiddleSchool->find('list'));
		$this->set('highSchools', $this->User->Profile->HighSchool->find('list'));
		$this->set('colleges', $this->User->Profile->College->find('list'));
		$this->set('publications', $this->User->Publication->find('list'));
		$this->set('jobCategories', $this->User->Profile->JobCategory->find('list'));
		$this->set('classifications', $this->User->Profile->Classification->find('list'));
		$this->set('campuses', $this->User->Profile->Campus->find('list'));
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
		$this->set('elementarySchools', $this->User->Profile->ElementarySchool->find('list'));
		$this->set('middleSchools', $this->User->Profile->MiddleSchool->find('list'));
		$this->set('highSchools', $this->User->Profile->HighSchool->find('list'));
		$this->set('colleges', $this->User->Profile->College->find('list'));
		$this->set('classifications', $this->User->Profile->Classification->find('list'));
	}

/**
 * Deletes a user
 *
 * @param integer $id The id of the User to delete
 */
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