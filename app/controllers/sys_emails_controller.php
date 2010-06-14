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
	var $helpers = array('Formatting');

/**
 * Extra components for this controller
 *
 * @var array
 */
	var $components = array('MultiSelect');

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
	}

/**
 * Creates a new bug report email
 */ 
	function bug_compose() {
		// hardcoded Jeremy Harris
		$toUsers = 1;
		
		$fromUser = $this->activeUser['User']['id'];
		
		if (!empty($this->data)) {
			// send email
			$this->set('message', $this->data['SysEmail']['body']);
			
			$this->SysEmail->set($this->data);
			
			// send it!
			if ($this->SysEmail->validates() && $this->QueueEmail->send(array(
				'from' => $fromUser, 
				'to' => $toUsers, 
				'subject' => $this->data['SysEmail']['subject']
			))) {
				$this->Session->setFlash('Message sent!', 'flash_success');
			} else {
				$this->Session->setFlash('Error sending message', 'flash_failure');
			}
		}
		
		if (empty($this->data)) {
			$bodyElement = 'email/bug_report';
			$this->data['SysEmail']['subject'] = 'Bug Report :: [enter short description here]';
		} else {
			$bodyElement = '';
		}
		
		$errors = ClassRegistry::init('Referee.Error')->find('all', array(
			'limit' => 10,
			'order' => 'Error.created DESC'
		));
		
		$this->set('errors', $errors);
		$this->set('visitHistory', array_reverse($this->Session->read('CoreDebugPanels.visitHistory')));
		$this->set('toUsers', array(ClassRegistry::init('User')->read(null, $toUsers)));
		$this->set('fromUser', ClassRegistry::init('User')->read(null, $fromUser));
		$this->set('cacheuid', false);
		$this->set('showAttachments', false);
		$this->set('bodyElement', $bodyElement);
		
		$this->render('compose');
	}
/**
 * Creates a new email
 *
 * Get's list of email addresses from previously cached search results.
 * Allows for an attachment. If the passed args are sent, the user email(s)
 * for that model and id will be used instead.
 *
 * ### Passed args:
 * - string $model A model to look up
 * - integer [$model] The id of the model
 *
 * @param string $uid The unique cache id of the list to pull
 */ 
	function compose($uid = null) {		
		$User = ClassRegistry::init('User');
		
		// check if we're emailing an involvement and we don't have a saved search
		if (isset($this->passedArgs['model']) && isset($this->passedArgs[$this->passedArgs['model']]) && !$uid) {
			switch ($this->passedArgs['model']) {
				case 'User':
					$toUsers = explode(',', $this->passedArgs[$this->passedArgs['model']]);
				break;
				case 'Involvement':
					$involvementRoster = ClassRegistry::init('Involvement')->find('all', array(
						'conditions' => array(
							'id' => $this->passedArgs[$this->passedArgs['model']]
						),
						'contain' => array(
							'Roster'
						)
					));
					
					$toUsers = Set::extract('/Roster/user_id', $involvementRoster);
				break;			
			}
			
			$search = array(
				'conditions' => array(
					'id' => $userIds
				),
				'contain' => array(
					'Profile'
				)
			);
			
			// no uid will be set, so set one and save this search
			$this->MultiSelect->saveSearch($search);
			// and select all since we know we want these
			$this->MultiSelect->selectAll();
			// finally, use this as the new multi select key
			$uid = $this->MultiSelect->cacheKey;
		} else {
			$search = $this->MultiSelect->getSearch($uid);
			$userIds = $this->MultiSelect->getSelected($uid);
			// assume they want all if they didn't select any
			if ($userIds != 'all' && !empty($userIds)) {
				$search['conditions']['User.id'] = $userIds;
			}
			
			$toUsers = $User->find('all', array_merge($search));
			$toUsers = Set::extract('/User/id', $toUsers);
		}
		
		$fromUser = $this->activeUser['User']['id'];
		
		if (!empty($this->data)) {
			// send email
			$this->set('message', $this->data['SysEmail']['body']);
			
			// get attachments for this email
			$Document = ClassRegistry::init('Document');
			$Document->recursive = -1;
			$documents = $Document->find('all', array(
				'conditions' => array(
					'foreign_key' => $uid
				)
			));
			
			$attachments = array();
			foreach ($documents as $attachment) {
				$attachments[] = $attachment['Document']['file'];
			}

			// attach them to the email
			$this->Email->attachments = $attachments;		
			
			$this->SysEmail->set($this->data);
			
			// send it!
			if ($this->SysEmail->validates() && $this->QueueEmail->send(array(
				'from' => $fromUser, 
				'to' => $toUsers, 
				'subject' => $this->data['SysEmail']['subject']
			))) {
				$this->Session->setFlash('Message sent!', 'flash_success');
				
				// delete attachments related with this email
				$this->SysEmail->gcAttachments($uid);
			} else {
				$this->Session->setFlash('Error sending message', 'flash_failure');
			}			
		} else {
			// clear old attachments that people aren't using anymore
			$this->SysEmail->gcAttachments();
		}
		
		$this->set('bodyElement', false);
		$this->set('toUsers', $User->find('all', array('conditions'=>array('User.id'=>$toUsers))));
		$this->set('fromUser', $User->read(null, $fromUser));
		$this->set('cacheuid', $uid);
		$this->set('showAttachments', true);
	}
}	
?>