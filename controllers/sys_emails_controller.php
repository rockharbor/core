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
	var $components = array(
		'MultiSelect.MultiSelect',
		'FilterPagination'
	);

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
 * List of human readable statuses
 *
 * @var array
 */
	var $statuses = array(
		0 => 'Queued',
		1 => 'Sent',
		2 => 'Sending'
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

		$this->_editSelf('index', 'view', 'html_email');

		$this->Auth->allow('bug_compose');

		// if the user is leading or managing, let them email people
		if ($this->activeUser['Profile']['leading'] > 0 || $this->activeUser['Profile']['managing'] > 0) {
			$this->Auth->allow('compose');
		}
	}

/**
 * Shows a list of emails to or from the user
 */
	function index() {
		$user = $this->passedArgs['User'];

		if (empty($this->data)) {
			$this->data = array(
				'Filter' => array(
					'show' => 'from',
					'hide_system' => 1
				)
			);
		}

		$this->paginate = array(
			'fields' => array(
				'COUNT(*) as message_count, SysEmail.*'
			),
			'contain' => array(
				'ToUser' => array(
					'Profile' => array(
						'fields' => array('name')
					)
				),
				'FromUser' => array(
					'Profile' => array(
						'fields' => array('name')
					)
				)
			),
			'group' => 'SysEmail.subject',
			'order' => 'modified DESC'
		);

		if ($this->data['Filter']['hide_system']) {
			$this->paginate['group'] .= ' HAVING SysEmail.from_id > 0';
		}

		switch ($this->data['Filter']['show']) {
			case 'from':
				$this->paginate['conditions']['SysEmail.from_id'] = $user;
				break;
			case 'both':
				$this->paginate['conditions']['or'] = array(
					'SysEmail.to_id' => $user,
					'SysEmail.from_id' => $user
				);
				break;
			default:
				$this->paginate['conditions']['SysEmail.to_id'] = $user;
		}

		$emails = $this->FilterPagination->paginate();
		$statuses = $this->statuses;

		$this->set(compact('emails', 'statuses'));
	}

/**
 * Shows a sent email
 *
 * @param integer $id The message id
 */
	function view($id = null) {
		if (!$id) {
			$this->cakeError('error404');
		}

		$user = $this->passedArgs['User'];

		$email = $this->SysEmail->find('first', array(
			'fields' => array(
				'id',
				'subject',
				'message',
				'from_id'
			),
			'conditions' => array(
				'or' => array(
					'SysEmail.to_id' => $user,
					'SysEmail.from_id' => $user
				),
				'SysEmail.id' => $id
			),
			'contain' => array(
				'FromUser' => array(
					'Profile' => array('name')
				)
			)
		));
		$this->set('email', $this->QueueEmail->interpret($email, 'SysEmail'));
		$this->set('user', $user);
	}

/**
 * Renders the complete HTML email message
 *
 * @param integer $id The message id
 */
	function html_email($id = null) {
		$this->layout = false;
		$this->view($id);
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
				$ministries = $this->_extractIds($this->Ministry, '/Ministry/id');
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
				$involvements = $this->_extractIds($this->Involvement, '/Involvement/id');
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
			$rosters = $this->_extractIds($this->Involvement->Roster, '/Roster/id');
			$users = $this->Involvement->Roster->find('all', array(
				'fields' => array(
					'user_id'
				),
				'conditions' => array(
					'id' => $rosters
				)
			));
			$this->users = Set::extract('/Roster/user_id', $users);
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
				if ($this->users == 'all') {
					$search = $this->MultiSelect->getSearch($token);
					if (empty($search)) {
						$search['conditions'] = array('User.id' => null);
					}
					$results = $this->User->find('all', $search);
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
		$user = $this->Involvement->Leader->findById($leaderId);
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
		// data was posted, so get the users
		if (!empty($this->data['SysEmail']['to'])) {
			$this->users = explode(',', $this->data['SysEmail']['to']);
		}

		if (empty($this->data) && (empty($this->users))) {
			$this->Session->setFlash('Invalid email list.', 'flash'.DS.'failure');
			return $this->redirect($this->emptyPage);
		}

		$fromUser = $this->activeUser;

		$toUserIds = $this->users;

		if (!empty($this->data)) {
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

				if (in_array($this->data['SysEmail']['email_users'], array('both', 'household_contact'))) {
					$households = $this->User->HouseholdMember->Household->getHouseholdIds($toUserIds);
					$contacts = $this->User->HouseholdMember->Household->find('all', array(
						'fields' => array(
							'contact_id'
						),
						'conditions' => array(
							'id' => $households
						)
					));
					$extraUsers = Set::extract('/Household/contact_id', $contacts);
					if ($this->data['SysEmail']['email_users'] == 'both') {
						$toUserIds = array_merge($toUserIds, $extraUsers);
					} else {
						$toUserIds = $extraUsers;
					}
				}

				$toUserIds = array_unique($toUserIds);
				$this->set('include_greeting', $this->data['SysEmail']['include_greeting']);
				$this->set('include_signoff', $this->data['SysEmail']['include_signoff']);

				foreach ($toUserIds as $toUser) {
					if ($this->Notifier->notify(array(
						'from' => $fromUser['User']['id'],
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
			// comma-delimited list of users to email
			$this->data['SysEmail']['to'] = implode(',', $toUserIds);
			// clear old attachments that people aren't using anymore
			$this->SysEmail->gcAttachments();
		}

		$this->set('toUserIds', $toUserIds);
		$this->set('toUsers', $this->User->find('all', array(
			'conditions' => array(
				'User.id' => $toUserIds
			),
			'contain' => array(
				'Profile'
			),
			'limit' => 20
		)));
		$this->set('fromUser', $fromUser);
		$this->set('showAttachments', true);
		$this->set('showPreferences', true);
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
