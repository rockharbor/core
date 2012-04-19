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
	var $components = array(
		'FilterPagination',
		'MultiSelect.MultiSelect',
		'Cookie',
		'Security' => array(
			'disabledFields' => array(
				'HouseholdMember'
			)
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
		
		// public actions
		$this->Auth->allow('login', 'logout', 'forgot_password', 'register', 'request_activation', 'choose_user');
		
		$this->_editSelf('edit');
	}
	
/**
 * Logs a user into CORE, saves their profile data in session
 *
 * @param string $username Used to auto-fill the username field
 * @todo Restrict login to users older than 12 (use Auth.userScope?)
 */
	function login($username = null) {
		// don't cache login page so _Tokens don't expire and blackhole
		$this->disableCache();
		
		// check for remember me checkbox
		if (!empty($this->data) && $this->data['User']['remember_me']) {
			unset($this->data['User']['remember_me']);
			$this->Session->delete('Message.auth');
			$this->Cookie->write('Auth.User', $this->data['User'], true, '+2 weeks');
		}

		// check for remember me cookie and use that data and reset the cookie
		$cookie = $this->Cookie->read('Auth.User');
		if (empty($this->data) && !is_null($cookie)) {
			$this->data['User'] = $cookie;
			$this->Cookie->write('Auth.User', $this->data['User'], true, '+2 weeks');
		}
		
		if (!empty($this->data)) {
			$authModel =& $this->Auth->getModel();
			$authModel->contain(array('Profile'));
			if ($this->Auth->login($this->data)) {
				$this->User->id = $this->Auth->user('id');
				$this->User->contain(array('Profile', 'Group', 'Image', 'ActiveAddress'));
				$this->Session->write('User', $this->User->read());
				$this->User->saveField('last_logged_in', date('Y-m-d H:i:s'));
				
				// force redirect if they need to reset their password
				if ($this->Auth->user('reset_password')) {				
					$this->Session->setFlash('Your password needs to be changed. Please reset it.', 'flash'.DS.'failure');
					return $this->redirect(array('controller' => 'users', 'action' => 'edit', 'User' => $this->Auth->user('id')));
				}
				return $this->redirect($this->Auth->redirect());
			} else {
				// trick into not redirecting and to highlighting fields
				$this->Cookie->delete('Auth');
				$this->Session->setFlash('Invalid username or password. Please try again.', 'flash'.DS.'failure');
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
		if (isset($this->passedArgs['message'])) {
			$this->Session->setFlash($this->passedArgs['message']);
		}
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
 * Creates a new password and sends it to the user, or offers them to reset
 */
	function edit() {
		$needCurrentPassword = $this->activeUser['User']['id'] == $this->passedArgs['User'];
		
		if (!empty($this->data)) {
			$invalidPassword = false;
			$this->User->id = $this->data['User']['id'];
			
			// check if they're resetting their username or password and stop validation for the other
			switch ($this->data['User']['reset']) {
				case 'username':
					unset($this->data['User']['password']);
					unset($this->data['User']['current_password']);
					unset($this->data['User']['confirm_password']);					
					// avoid needing a password to save
					$success = $this->User->saveField('username', $this->data['User']['username']);
					$this->set('username', $this->data['User']['username']);
				break;
				case 'password':
					unset($this->data['User']['username']);
					if ($needCurrentPassword && $this->Auth->password($this->data['User']['current_password']) != $this->User->field('password')) {
						$invalidPassword = true;
					}
					$this->User->set($this->data);
					if (!$invalidPassword && $this->User->validates()) {
						$this->User->id = $this->data['User']['id'];
						$success = $this->User->saveField('password', $this->data['User']['password']);
						if ($this->activeUser['User']['id'] != $this->passedArgs['User']) {
							$this->User->saveField('reset_password', true);
						} else {
							$this->User->saveField('reset_password', false);
						}
					} else {
						$success = false;
					}
					$this->set('password', $this->data['User']['password']);
				break;
				case 'both':
					if ($this->activeUser['User']['id'] != $this->passedArgs['User']) {
						$this->data['User']['reset_password'] = true;
					} else {
						$this->data['User']['reset_password'] = false;
					}
					$success = $this->User->save($this->data);
					$this->set('username', $this->data['User']['username']);
					$this->set('password', $this->data['User']['password']);
				break;
			}
			
			if ($success) {
				$this->Session->setFlash('This user as been updated.', 'flash'.DS.'success');
				$this->set('reset', $this->data['User']['reset']);
				$this->Notifier->notify(array(
					'to' => $this->data['User']['id'],
					'subject' => 'Your account settings have changed',
					'template' => 'users_edit'
				), 'email');
			} else {
				if ($invalidPassword) {
					$this->User->invalidate('current_password', 'Incorrect password. Please try again.');
				}
				$this->Session->setFlash('Unable to save this user. Please try again.', 'flash'.DS.'failure');
			}
		}
		
		if (empty($this->data)) {
			$this->User->id = $this->passedArgs['User'];
			$this->User->contain(false);
			$this->data = $this->User->read();
			unset($this->data['User']['password']);
		}
		
		$this->set('needCurrentPassword', $needCurrentPassword);
	}

/**
 * Sends a user a new password
 */
	function forgot_password($id = null) {
		if ((!empty($this->data) || !is_null($id)) && !isset($this->passedArgs['skip_check'])) {
			if (!$id) {
				$searchData = array(
					'User' => array(
						'username' => $this->data['User']['forgotten']
					),
					'Profile' => array(
						'primary_email' => $this->data['User']['forgotten']
					)
				);
				$user = $this->User->findUser($searchData, 'OR');
				if (count($user) == 0) {
					$user = false;
				} elseif (count($user) > 1) {
					return $this->setAction('choose_user', $user, array(
						'controller' => 'users',
						'action' => 'forgot_password',
						':ID:'
					), array('action' => 'forgot_password'));
				} else {
					$user = $user[0];
				}
			} else{
				$user = $id;
			}
			
			if ($user) {
				$this->User->id = $user;
		
				$newPassword = $this->User->generatePassword();
		
				if ($this->User->saveField('password', $newPassword)) {
					$this->User->saveField('reset_password', true);
					$this->Session->setFlash('Your new password has been sent to your email address.', 'flash'.DS.'success');
					$username = $this->User->field('username');
					$this->set('password', $newPassword);
					$this->set('username', $username);
					$this->Notifier->notify(array(
						'to' => $user,
						'subject' => 'Your password has been reset',
						'template' => 'users_forgot_password'
					), 'email');
				} else {
					$this->Session->setFlash('Unable to process request. Please try again.', 'flash'.DS.'failure');
				}
			} else {
				$this->Session->setFlash('User not found. Please try again.', 'flash'.DS.'failure');
			}
		}
		
		if (isset($this->passedArgs['skip_check'])) {
			$this->Session->setFlash('Try searching on something more specific to you.', 'flash'.DS.'success');
			$this->here = str_replace('skip_check', 'skipped', $this->here);
		}
	}

/**
 * Allows a user to choose from a list of users found via `User::findUser()` and
 * redirect them to another action. 
 * 
 * This action should only be called via `setAction` so arrays can be passed and 
 * data is preserved. The redirect url can be an array or string, but _must_ 
 * contain the special passed parameter `:ID:`. This will be replaced with the 
 * id that the user chooses.
 * 
 * A named parameter `skip_check` will be appended to `$return`. It's the
 * responsibility of the return action to act appropriately and skip the `User::findUser()`
 * check that brought them here in the first place, otherwise they will be directed
 * here again (since the data is persisted).
 * 
 * @param array $users The array of user ids returned by `User::findUser()`
 * @param mixed $redirect The redirect string or Cake url array
 * @param string $return The url to return to if the user decides there aren't any matches
 */
	function choose_user($users = array(), $redirect = '/users/request_activation/:ID:/1', $return = null) {
		if (empty($users) || !$return) {
			$this->redirect($this->referer());
		}
		// need full path because FormHelper prepends the controller name to the url 
		// (which already has one defined) 
		$redirect = Router::url($redirect, true);
		$return = Router::url($return, true);
		$return = trim($return, '/').'/skip_check:1';

		$users = $this->User->find('all', array(
			'fields' => array(
				'id'
			),
			'conditions' => array(
				'User.id' => $users
			),
			'contain' => array(
				'Profile' => array(
					'fields' => array('first_name', 'last_name')
				),
				'ActiveAddress' => array(
					'fields' => array('city')
				)
			)
		));
		$this->set(compact('redirect', 'users', 'return'));
	}

/**
 * Creates a profile for the user and a merge request
 *
 * @param integer $foundId The id of the user to merge with
 * @param boolean $initialRedirect True if came directly from UsersController::add()
 */ 
	function request_activation($foundId, $initialRedirect = false) {		
		if (!empty($this->data) && !$initialRedirect && $foundId) {		
			$this->data['User']['active'] = false;	
			$this->data['Address'][0]['model'] = 'User';
			
			// create near-empty user for now (for merging)
			if ($this->User->createUser($this->data, null, $this->activeUser, false)) {
				// save merge request
				$MergeRequest = ClassRegistry::init('MergeRequest');
				$MergeRequest->save(array(
					'model' => 'User',
					'model_id' => $this->User->id,
					'merge_id' => $foundId,
					'requester_id' => $this->User->id
				));
				$this->Notifier->notify(array(
					'to' => Core::read('notifications.activation_requests'),
					'template' => 'users_request_activation',
					'subject' => 'Account merge request',
				));
				$this->Session->setFlash('Account merge request has been received.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Unable to send account merge request. Please try again.', 'flash'.DS.'failure');
			}			
		}
		
		$this->set('foundId', $foundId);
	}
	
/**
 * Adds a user
 */
	function add() {
		if (!empty($this->data)) {
			$foundUser = array();
			if (!isset($this->passedArgs['skip_check'])) {
				// check if user exists (only use profile info to search)
				$searchData = array('Profile' => $this->data['Profile']);
				$searchData['Profile']['email'] = $searchData['Profile']['primary_email'];
				$foundUser = $this->User->findUser($searchData, 'OR');
			}

			if (!empty($foundUser)) {
				// take to activation request (preserve data)
				if (count($foundUser) == 1) {
					return $this->setAction('request_activation', $foundUser[0], true);
				} else {
					return $this->setAction('choose_user', $foundUser, array(
						'controller' => 'users',
						'action' => 'request_activation',
						':ID:',
						true
					), array('action' => 'add'));
				}
			}

			if ($this->User->createUser($this->data, null, $this->activeUser)) {
				foreach ($this->User->tmpAdded as $notifyUser) {
					$this->set('username', $notifyUser['username']);
					$this->set('password', $notifyUser['password']);
					$this->Notifier->notify(array(
						'to' => $notifyUser['id'],
						'template' => 'users_register',
						'subject' => 'Welcome to '.Core::read('general.site_name_tagless').'!'
					));
				}

				foreach ($this->User->tmpInvited as $notifyUser) {
					$this->User->contain(array('Profile'));
					$this->set('contact', $this->User->read(null, $this->User->id));
					$this->Notifier->invite(
						array(
							'to' => $notifyUser['id'],
							'template' => 'households_invite',
							'confirm' => '/households/confirm/'.$notifyUser['id'].'/'.$this->User->HouseholdMember->Household->id,
							'deny' => '/households/delete/'.$notifyUser['id'].'/'.$this->User->HouseholdMember->Household->id
						)
					);
				}

				$this->Session->setFlash('An account has been created for '.$this->data['Profile']['first_name'].' '.$this->data['Profile']['last_name'].'.', 'flash'.DS.'success');
			} else {		
				$this->Session->setFlash('Unable to create account. Please try again.', 'flash'.DS.'failure');
			}
		}
		
		$this->_prepareAdd();
	}

/**
 * Creates a user account and adds it to a household
 */
	function household_add() {
		// require birthday
		$this->User->Profile->validate['birth_date']['required'] = true;
		$this->User->Profile->validate['birth_date']['allowEmpty'] = false;
		
		if (!empty($this->data)) {
			// check if user exists (only use profile info to search)
			$searchData = array('Profile' => $this->data['Profile']);
			$searchData['Profile']['email'] = $searchData['Profile']['primary_email'];
			$foundUser = $this->User->findUser($searchData, 'OR');
			if (!empty($foundUser) && !isset($this->passedArgs['skip_check'])) {
				// take to choose user
				// - takes them to Households::invite() if a match is found
				// - takes them back here, otherwise
				return $this->setAction('choose_user', $foundUser, array(
					'controller' => 'households',
					'action' => 'invite',
					':ID:',
					$this->data['Household']['id']
				), array('action' => 'household_add', 'Household' => $this->data['Household']['id']));
			}

			if ($this->User->createUser($this->data, $this->data['Household']['id'], $this->activeUser)) {
				$name = $this->data['Profile']['first_name'].' '.$this->data['Profile']['last_name'];
				$this->Session->setFlash('An account for '.$name.' has been created. '.$name.' has also been added to your household.', 'flash'.DS.'success');

				$this->set('username', $this->data['User']['username']);
				$this->set('password', $this->data['User']['password']);
				$this->Notifier->notify(array(
					'to' => $this->User->id,
					'template' => 'users_register',
					'subject' => 'Account registration'
				));
				
				$this->set('contact', $this->activeUser);
				$this->set('joined', $this->User->Profile->findByUserId($this->User->id));
				$this->Notifier->notify(array(
					'to' => $this->User->id,
					'template' => 'households_join',
					'subject' => 'You have joined '.$this->activeUser['Profile']['name'].'\'s household'
				));
				
				$this->redirect(array(
					'controller' => 'households',
					'action' => 'index'
				));
			} else {
				$this->Session->setFlash('Oops, validation errors...', 'flash'.DS.'failure');
			}
		}
		
		if (!isset($this->data['Household'])) {
			$this->data['Household']['id'] = $this->passedArgs['Household'];
		}
		
		if (isset($this->passedArgs['skip_check'])) {
			$this->Session->setFlash('Finish filling out the form to add a new user.', 'flash'.DS.'success');
		}
		
		// household contact's addresses
		$this->User->HouseholdMember->Household->contain(array(
			'HouseholdContact' => array(
				'Address'
			)
		));
		$householdContact = $this->User->HouseholdMember->Household->read(null, $this->data['Household']['id']);
		$this->set('addresses', $householdContact['HouseholdContact']);
		
		$this->_prepareAdd();
	}
	
/**
 * Registers a user
 */
	function register() {
		// require birthday
		$this->User->Profile->validate['birth_date']['required'] = true;
		$this->User->Profile->validate['birth_date']['allowEmpty'] = false;
		
		if (!empty($this->data)) {
			$foundUser = array();
			if (!isset($this->passedArgs['skip_check'])) {
				// check if user exists (only use profile info to search)
				$searchData = array('Profile' => $this->data['Profile']);
				$searchData['Profile']['email'] = $searchData['Profile']['primary_email'];
				$foundUser = $this->User->findUser($searchData, 'OR');
			}

			if (!empty($foundUser)) {
				// take to activation request (preserve data)
				if (count($foundUser) == 1) {
					return $this->setAction('request_activation', $foundUser[0], true);
				} else {
					return $this->setAction('choose_user', $foundUser, array(
						'controller' => 'users',
						'action' => 'request_activation',
						':ID:',
						true
					), array('action' => 'register'));
				}
			}
			
			if ($this->User->createUser($this->data)) {
				$this->Session->setFlash('You have successfully registered.', 'flash'.DS.'success');

				foreach ($this->User->tmpAdded as $notifyUser) {
					$this->set('username', $notifyUser['username']);
					$this->set('password', $notifyUser['password']);
					$this->Notifier->notify(array(
						'to' => $notifyUser['id'],
						'template' => 'users_register',
						'subject' => 'Welcome to '.Core::read('general.site_name_tagless').'!'
					));
				}

				foreach ($this->User->tmpInvited as $notifyUser) {
					$this->User->contain(array('Profile'));
					$this->set('contact', $this->User->read(null, $this->User->id));
					$this->Notifier->invite(
						array(
							'to' => $notifyUser['id'],
							'template' => 'households_invite',
							'confirm' => '/households/confirm/'.$notifyUser['id'].'/'.$this->User->HouseholdMember->Household->id,
							'deny' => '/households/delete/'.$notifyUser['id'].'/'.$this->User->HouseholdMember->Household->id
						)
					);
				}

				$this->redirect(array(
					'controller' => 'users',
					'action' => 'login',
					$this->data['User']['username']
				));
			} else {
				$this->Session->setFlash('Unable to register. Please try again.', 'flash'.DS.'failure');
			}
		}

		$this->_prepareAdd();
	}
	
/**
 * Common code used in `Users::household_add()`, `Users::add()` and `Users::register()`
 */
	function _prepareAdd() {
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
 * Admin dashboard
 */
	function dashboard() {
		$controllers = array('job_categories', 'schools', 'regions', 'classifications', 'payment_types', 'involvement_types', 'roster_statuses', 'publications');
		$this->set(compact('controllers'));
	}

/**
 * Deletes a user
 *
 * @param integer $id The id of the User to delete
 */
	function delete($id = null) {		
		if (!$id) {
			$this->cakeError('error404');
		}
		if ($this->User->delete($id)) {
			$this->Session->setFlash(__('User record has been removed.', true), 'flash'.DS.'success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Unable to remove user. Please try again.', true), 'flash'.DS.'failure');
		$this->redirect(array('action' => 'index'));
	}
}
?>