<?php
/**
 * Sys Email controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * SysEmails Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class SysEmailsController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'SysEmails';

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array('Formatting', 'MultiSelect.MultiSelect');

/**
 * Extra components for this controller
 *
 * @var array
 */
	var $components = array('MultiSelect.MultiSelect');
	
/**
 * Models used by this controller
 * 
 * @var array
 */
	var $uses = array('SysEmail', 'User', 'Involvement', 'Ministry');
	
/**
 * Users to email
 * 
 * This var is only used on the initial request, afterwhich the data value for
 * `$this->data['SysEmail']['to']` is passed between requests. This prevents
 * the need for looking up the user search on each request.
 * 
 * @var array
 */
	var $users = array();

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('bug_compose');
		
		// if the user is leading or managing, let them email people
		if ($this->activeUser['Profile']['leading'] > 0 || $this->activeUser['Profile']['managing'] > 0) {
			$this->Auth->allow('compose');
		}
	}

/**
 * Creates a new bug report email
 */ 
	function bug_compose() {
		$this->set('title_for_layout', 'Submit a bug report');
		$User = ClassRegistry::init('User');
		$User->contain(array('Profile'));
		// hardcoded Jeremy Harris
		$jeremy = $User->findByUsername('jharris');
		
		if (!empty($this->data)) {
			$this->SysEmail->set($this->data);
			
			// send it!
			if ($this->SysEmail->validates() && $this->Notifier->notify(array(
				'from' => $this->activeUser['User']['id'], 
				'to' => $jeremy['User']['id'], 
				'queue' => false,
				'subject' => $this->data['SysEmail']['subject'],
						'body' => $this->data['SysEmail']['body']
			), 'email')) {
				$this->Session->setFlash('Your email has been sent.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Unable to send email.', 'flash'.DS.'failure');
			}
		}
		
		$this->set('visitHistory', array_reverse($this->Session->read('CoreDebugPanels.visitHistory')));
		$this->set('toUsers', array($jeremy));
		$this->set('fromUser', $this->activeUser);
		$this->set('cacheuid', false);
		$this->set('showAttachments', false);
		// needed for element
		$this->set('activeUser', $this->activeUser);
		
		if (empty($this->data)) {
			$this->data['SysEmail']['subject'] = 'Bug Report :: [enter short description here]';
			
			App::import('View', 'view');
			$View = new View($this->Controller, false);
			$View->webroot = WEBROOT_DIR;
			$content = $View->element('email' . DS . 'bug_report', $this->viewVars, true);
			
			$this->data['SysEmail']['body'] = $content;
		}
		
		$this->render('compose');
	}
	
/**
 * Emails users or leaders from a Ministry
 * 
 * By passing an id to the `Ministry` passed arg, you can email a single
 * Ministry user group (see `$group`). Or, use multiselect to select a group 
 * of ministry ids, from which the user groups will be pulled.
 * 
 * @param string $group 'leaders', 'users', or 'both'
 */		
	function ministry($group = 'users') {
		if (empty($this->data['SysEmail']['to'])) {
			if (isset($this->passedArgs['Ministry'])) {
				$ministries = array($this->passedArgs['Ministry']);
			} else {
				$token = $this->params['named']['mstoken'];
				$ministries = $this->MultiSelect->getSelected($token);
				if (empty($ministries)) {
					$search = $this->MultiSelect->getSearch($token);
					$results = $this->Ministry->find('all', $search);
					$ministries = Set::extract('/Ministry/id', $results);
				}
			}
			
			$this->users = $this->_getUsers('Ministry', $ministries, $group);
		}
		$this->setAction('compose');
	}
	
/**
 * Emails users or leaders from an Involvement
 * 
 * By passing an id to the `Involvement` passed arg, you can email a single
 * Involvement user group (see `$group`). Or, use multiselect to select a group 
 * of involvement ids, from which the user groups will be pulled.
 * 
 * @param string $group 'leaders', 'users', or 'both'
 */		
	function involvement($group = 'users') {
		if (empty($this->data['SysEmail']['to'])) {
			if (isset($this->passedArgs['Involvement'])) {
				$involvements = array($this->passedArgs['Involvement']);
			} else {
				$token = $this->params['named']['mstoken'];
				$involvements = $this->MultiSelect->getSelected($token);
				if (empty($involvements)) {
					$search = $this->MultiSelect->getSearch($token);
					$results = $this->Involvement->find('all', $search);
					$involvements = Set::extract('/Involvement/id', $results);
				}
			}
			
			$this->users = $this->_getUsers('Involvement', $involvements, $group);
		}
		$this->setAction('compose');
	}
	
/**
 * Emails a user from roster record
 * 
 * Use multiselect to select a group of roster ids, from which the user ids will
 * be pulled.
 */	
	function roster() {
		if (empty($this->data['SysEmail']['to'])) {
			$token = $this->params['named']['mstoken'];
			$rosters = $this->MultiSelect->getSelected($token);
			if (empty($rosters)) {
				$search = $this->MultiSelect->getSearch($token);
				$results = $this->Involvement->Roster->find('all', $search);
			} else {
				$results = $this->Involvement->Roster->find('all', array(
					'fields' => array(
						'user_id'
					),
					'conditions' => array(
						'id' => $rosters
					)
				));
			}
			
			$this->users = Set::extract('/Roster/user_id', $results);
		}
		$this->setAction('compose');
	}

/**
 * Emails a user
 * 
 * By passing an id to the named param `User` you can email a specific user. 
 * Or, use multiselect to select a group of user ids
 */
	function user() {
		if (empty($this->data['SysEmail']['to'])) {
			if (isset($this->passedArgs['User'])) {
				$this->users = array($this->passedArgs['User']);
			} else {
				$token = $this->params['named']['mstoken'];
				$this->users = $this->MultiSelect->getSelected($token);
				if (empty($this->users)) {
					$search = $this->MultiSelect->getSearch($token);
					$results = ClassRegistry::init('User')->find('all', $search);
					$this->users = Set::extract('/User/id', $results);
				}
			}
		}
		$this->setAction('compose');
	}
	
/**
 * Pass-through function to allow regular users to email leaders
 * 
 * @param integer $leaderId The leader id
 */
	function leader($leaderId) {
		$Leader = ClassRegistry::init('Leader');
		$user = $Leader->findById($leaderId);
		$this->users = array($user['Leader']['user_id']);
		$this->setAction('compose');
	}
	
/**
 * Creates a new email
 * 
 * This action should not be used directly. It relies on `$this->users` to be 
 * set by a preceding action.
 */ 
	function compose() {
		if (empty($this->data) && (empty($this->users) && empty($this->data['SysEmail']['to']))) {
			$this->Session->setFlash('Invalid email list.', 'flash'.DS.'failure');
			return $this->redirect($this->emptyPage);
		}
		
		$User = ClassRegistry::init('User');

		$fromUser = $this->activeUser['User']['id'];
		
		if (empty($this->data['SysEmail']['to'])) {
			$this->data['SysEmail']['to'] = implode(',', $this->users);
		}
		
		if (!empty($this->data) && empty($this->users)) {
			// get attachments for this email
			$Document = ClassRegistry::init('Document');
			$Document->recursive = -1;
			$documents = $Document->find('all', array(
				'conditions' => array(
					'foreign_key' => $this->params['named']['mstoken'],
					'model' => 'SysEmail'
				)
			));
			
			$attachments = array();
			foreach ($documents as $attachment) {
				list($filename, $ext) = explode('.', $attachment['Document']['basename']);
				$attachments[$attachment['Document']['alternative'].'.'.$ext] = $attachment['Document']['file'];
			}

			$this->SysEmail->set($this->data);
			
			// send it!
			if ($this->SysEmail->validates()) {
				$e = 0;
				
				$toUsers = explode(',', $this->data['SysEmail']['to']);
				$toUsers = array_unique($toUsers);
				
				if (in_array($this->data['SysEmail']['email_users'], array('both', 'household_contact'))) {
					$households = $User->HouseholdMember->Household->getHouseholdIds($toUsers);
					$contacts = $User->HouseholdMember->Household->find('all', array(
						'fields' => array(
							'contact_id'
						),
						'conditions' => array(
							'id' => $households
						)
					));
					$extraUsers = Set::extract('/Household/contact_id', $contacts);
					if ($this->data['SysEmail']['email_users'] == 'both') {
						$toUsers = array_merge($toUsers, $extraUsers);
					} else {
						$toUsers = $extraUsers;
					}
				}
				
				$toUsers = array_unique($toUsers);
				$this->set('allToUsers', $toUsers);

				foreach ($toUsers as $toUser) {
					if ($this->Notifier->notify(array(
						'from' => $fromUser,
						'to' => $toUser,
						'subject' => $this->data['SysEmail']['subject'],
						'attachments' => $attachments,
						'body' => $this->data['SysEmail']['body']
					), 'email')) {
						$e++;
					}
				}

				$this->Session->setFlash('Your emails have been sent.', 'flash'.DS.'success');
				
				// delete attachments related with this email
				$this->SysEmail->gcAttachments($this->MultiSelect->_token);
			} else {
				$this->Session->setFlash('Unable to send your emails.', 'flash'.DS.'failure');
			}			
		} else {
			// clear old attachments that people aren't using anymore
			$this->SysEmail->gcAttachments();
		}
		
		$User->contain(array(
			'Profile' => array(
				'fields' => array(
					'id','name','primary_email'
				)
			)
		));
		$this->set('toUsers', $User->find('all', array('conditions'=>array('User.id'=>explode(',', $this->data['SysEmail']['to'])))));
		$User->contain(array(
			'Profile' => array(
				'fields' => array(
					'id','name','primary_email'
				)
			)
		));
		$this->set('fromUser', $User->read(null, $fromUser));
		$this->set('showAttachments', true);
	}

/**
 * Gets a list of users from a particular model and user group
 * 
 * @param string $model The model (Involvement or Ministry)
 * @param array $ids Array of model ids
 * @param string $group 'leaders', 'users', or 'both'
 * @return array Array of user ids 
 */
	function _getUsers($model, $ids, $group = 'users') {
		if (empty($ids)) {
			return array();
		}
		switch ($group) {
			case 'both':
				$leaders = $this->{$model}->getLeaders($ids);
				$involved = $this->{$model}->getInvolved($ids);
				$users = array_merge($involved, $leaders);
			break;
			case 'leaders':
				$users = $this->{$model}->getLeaders($ids);
			break;
			default:
				$users = $this->{$model}->getInvolved($ids);
			break;
		}
		return $users;
	}
}	
?>