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
			$this->set('content', $this->data['SysEmail']['body']);
			
			$this->SysEmail->set($this->data);
			
			// send it!
			if ($this->SysEmail->validates() && $this->Notifier->notify(array(
				'from' => $fromUser, 
				'to' => $toUsers, 
				'subject' => $this->data['SysEmail']['subject']
			), 'email')) {
				$this->Session->setFlash('Message sent!', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Error sending message', 'flash'.DS.'failure');
			}
		}
		
		if (empty($this->data)) {
			$bodyElement = 'email'.DS.'bug_report';
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
 * Allows for an attachment.
 *
 * There are a few different options for mass emails. The first is by simply
 * supplying two passed args, `model` and `$model`. These will run the `getInvolved()`
 * function on the passed model.
 *
 * If a `model` passed arg is sent as well as a multi-select id, all of those
 * id's for that model will be pulled.
 *
 * Additionally, you can pull the 'Leaders' or 'Managers' of specific models by
 * passing the `submodel` passed arg along with the `model` passed arg, where
 * `submodel` is Leader, Roster or Manager. Note: Managers are leaders who are
 * *above* the current model, i.e., Involvement managers are leaders of that
 * Involvement's Ministry.
 *
 * If no passed args are sent but a multi-select id is, it's assumed that the
 * selections are Users.
 *
 * ### Passed args:
 * - string $model A model to look up
 * - integer [$model] The id of the model
 * - string `submodel` A special key for mass emails, like emailing all of the
 * leaders within a set of involvements. Valid: 'Leader', 'Roster'
 *
 * @param string $uid The unique cache id of the list to pull
 */ 
	function compose($uid = null) {
		// clear old attachments that people aren't using anymore
		$this->SysEmail->gcAttachments();
		$User = ClassRegistry::init('User');

		$modelIds = $this->MultiSelect->getSelected($uid);
		$toUsers = $modelIds;
		if (isset($this->passedArgs['model'])) {
			if (isset($this->passedArgs[$this->passedArgs['model']])) {
				$modelIds = array($this->passedArgs[$this->passedArgs['model']]);
			}			
			if (isset($this->passedArgs['submodel'])) {
				switch ($this->passedArgs['submodel']) {
					case 'Leader':
						$toUsers = ClassRegistry::init($this->passedArgs['model'])->getLeaders($modelIds);
					break;
					case 'Manager':
						$toUsers = ClassRegistry::init('Leader')->getManagers($this->passedArgs['model'], $modelIds);
					break;
					default:
						$toUsers = ClassRegistry::init($this->passedArgs['model'])->getInvolved($modelIds);
					break;
				}
			} else {
				switch ($this->passedArgs['model']) {
					case 'User':
						$toUsers = $modelIds;
					break;
					default:
						$toUsers = ClassRegistry::init($this->passedArgs['model'])->getInvolved($this->passedArgs[$this->passedArgs['model']]);
					break;
				}
			}
		}

		$fromUser = $this->activeUser['User']['id'];
		
		if (!empty($this->data)) {
			// send email
			$this->set('content', $this->data['SysEmail']['body']);
			
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
				$attachments[] = $attachment['Document']['dirname'].DS.$attachment['Document']['basename'];
			}

			// attach them to the email
			$this->Email->attachments = $attachments;		
			
			$this->SysEmail->set($this->data);
			
			// send it!
			if ($this->SysEmail->validates()) {
				$e = 0;
				foreach ($toUsers as $toUser) {
					if ($this->Notifier->notify(array(
						'from' => $fromUser,
						'to' => $toUser,
						'subject' => $this->data['SysEmail']['subject']
					), 'email')) {
						$e++;
					}
				}

				$this->Session->setFlash('Successfully sent '.$e.'/'.count($toUsers).' messages', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Error sending messages', 'flash'.DS.'failure');
			}			
		}

		$User->contain(array(
			'Profile' => array(
				'fields' => array(
					'id','name','primary_email'
				)
			)
		));
		$this->set('bodyElement', false);
		$this->set('toUsers', $User->find('all', array('conditions'=>array('User.id'=>$toUsers))));
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
}	
?>