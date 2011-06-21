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
				'HouseholdMember.Profile'
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
		$this->Auth->allow('login', 'logout', 'forgot_password', 'register', 'request_activation');
		
		$this->_editSelf('edit');
	}
	
/**
 * Logs a user into CORE, saves their profile data in session
 *
 * @param string $username Used to auto-fill the username field
 * @todo Restrict login to users older than 12 (use Auth.userScope?)
 */
	function login($username = null) {
		// check for remember me checkbox
		if (!empty($this->data) && $this->data['User']['remember_me']) {
			unset($this->data['User']['remember_me']);
			$this->Session->delete('Message.auth');
			$this->Cookie->write('Auth.User', $this->data['User'], false, '+2 weeks');
		}

		// check for remember me cookie and use that data
		if (empty($this->data) && !is_null($this->Cookie->read('Auth.User'))) {
			$this->data['User'] = $this->Cookie->read('Auth.User');
		}
		
		if (!empty($this->data)) {
			if ($this->Auth->login($this->data)) {
				$this->User->id = $this->Auth->user('id');
				$this->User->saveField('last_logged_in', date('Y-m-d H:i:s'));

				$this->User->contain(array('Profile', 'Group', 'Image', 'ActiveAddress'));
				$this->Session->write('User', $this->User->read());
			
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
				break;
				case 'password':
					unset($this->data['User']['username']);
					if ($needCurrentPassword && $this->Auth->password($this->data['User']['current_password']) != $this->User->field('password')) {
						$invalidPassword = true;
					}
					// avoid needing a username to save
					if ($this->User->validates(array('fieldList' => array('password', 'confirm_password')))) {
						$this->User->id = $this->data['User']['id'];
						$success = $this->User->saveField('password', $this->data['User']['password']);
						$this->User->saveField('reset_password', false);
					} else {
						$success = false;
					}
					$this->set('password', $this->data['User']['password']);
				break;
				case 'both':
					$this->data['User']['reset_password'] = false;
					$success = $this->User->save($this->data);
					$this->set('username', $this->data['User']['username']);
					$this->set('password', $this->data['User']['password']);
				break;
			}
			
			if ($success) {
				$this->Session->setFlash('Please log in with your updated account information.', 'flash'.DS.'success');
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
					$this->User->saveField('reset_password', true);
					$this->Session->setFlash('Your new password has been sent to your email address.', 'flash'.DS.'success');
					$this->set('password', $newPassword);
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
				$this->Notifier->notify(array(
					'to' => Core::read('notifications.activation_requests'),
					'template' => 'users_request_activation',
					'subject' => 'Account merge request',
				));
				$this->Session->setFlash('Account merge request has been received.', 'flash'.DS.'success');
				$this->redirect('/');
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
			// check if user exists
			$foundUser = $this->User->findUser(array(
				$this->data['User']['username'],
				$this->data['Profile']['primary_email'],
				$this->data['Profile']['first_name'],
				$this->data['Profile']['last_name']
			));

			if ($foundUser !== false) {
				$this->Session->setFlash('That user already exists.', 'flash'.DS.'failure');
				$this->redirect(array('controller' => 'profiles', 'action' => 'view', 'User' => $foundUser));
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
					$this->set('notifier', $this->User->read(null, $this->activeUser['User']['id']));
					$this->set('contact', $this->User->read(null, $this->User->id));
					$this->Notifier->notify(array(
						'to' => $notifyUser['id'],
						'template' => 'households_invite',
						'type' => 'invitation',
					), 'notification');
				}

				$this->Session->setFlash('An account has been created for '.$this->data['Profile']['first_name'].' '.$this->data['Profile']['last_name'].'.', 'flash'.DS.'success');

				$this->redirect(array(
					'controller' => 'users',
					'action' => 'index'
				));
			} else {		
				$this->Session->setFlash('Unable to create account. Please try again.', 'flash'.DS.'failure');
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
					$this->set('notifier', $this->User->read(null, $this->User->id));
					$this->set('contact', $this->User->read(null, $this->User->id));
					$this->Notifier->notify(array(
						'to' => $notifyUser['user'],
						'template' => 'households_invite',
						'type' => 'invitation'
					), 'notification');
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

		$this->set('elementarySchools', $this->User->Profile->ElementarySchool->find('list'));
		$this->set('middleSchools', $this->User->Profile->MiddleSchool->find('list'));
		$this->set('highSchools', $this->User->Profile->HighSchool->find('list'));
		$this->set('colleges', $this->User->Profile->College->find('list'));
		$this->set('publications', $this->User->Publication->find('list'));
		$this->set('jobCategories', $this->User->Profile->JobCategory->find('list'));
		$this->set('classifications', $this->User->Profile->Classification->find('list'));
		$this->set('campuses', $this->User->Profile->Campus->find('list'));

		$this->autoRender = false;
		$this->render('add');
	}

/**
 * Admin dashboard
 */
	function dashboard() {
		$controllers = array('job_categories', 'schools', 'regions', 'classifications', 'payment_types', 'involvement_types', 'roster_statuses');
		$this->set(compact('controllers'));
	}

/**
 * Deletes a user
 *
 * @param integer $id The id of the User to delete
 */
	function delete($id = null) {		
		if (!$id) {
			//404
			$this->Session->setFlash(__('Invalid id for user', true));
			$this->redirect(array('action'=>'index'));
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