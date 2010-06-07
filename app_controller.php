<?php
/**
 * Short description for file.
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


class AppController extends Controller {

/**
 * CORE's version
 *
 * @var string
 * @access public
 */		
	var $_version = '2.0.0-alpha';
	
/**
 * Stored global CORE settings
 *
 * @var array
 * @access public
 */		
	var $CORE = null;

/**
 * Stored visit history
 *
 * @var array
 * @access public
 */		
	var $_visitHistory = array();
	
	var $components = array(
		'Session',
		'Email',
		'DebugKit.Toolbar',
		'RequestHandler',
		'Acl',
		'Auth' => array(
			'authorize' => 'controller',
			'actionPath' => 'controllers/',
			'allowedActions' => array(
				'logout'
			),
			'authError' => 'Please login to continue.',
			'autoRedirect' => false,
			'loginAction' => array(
				'controller' => 'users', 
				'action' => 'login'
			),
			'logoutRedirect' => array(
				'controller' => 'users', 
				'action' => 'login'
			),
			'loginRedirect' => array(
				'controller' => 'pages', 
				'action' =>'display', 
				'home'
			),
			'userScope' => array('User.active' => true)
		),
		'Referee.Whistle',
		'Notifier' => array(
			'saveData' => array(
				'type' => 'default'
			)
		)
	);
	
	var $helpers = array(
		'Js' => array('Jquery'),
		'Session',
		'Text'
	);


/**
 * Default callbacks for ajax submit buttons
 *
 * @var array
 * @access public
 */	
	var $defaultSubmitOptions = array(
		'before' => 'CORE.beforeForm(event, XMLHttpRequest);',
		'complete' => 'CORE.completeForm(event, XMLHttpRequest, textStatus)',
		'success' => 'CORE.successForm(event, data, textStatus)',
		'error' => 'CORE.errorForm(event, XMLHttpRequest, textStatus, errorThrown)',
		'evalScripts' =>  true
	);
	
/**
 * Empty page as Cake url
 *
 * @var array
 * @access public
 */	
	var $emptyPage = array(
		'controller' => 'pages',
		'action' => 'display',
		'empty'
	);
	
/**
 * Failed authorization page as Cake url
 *
 * @var array
 * @access public
 */	
	var $authPage = array(
		'controller' => 'pages',
		'action' => 'display',
		'auth_fail'
	);

/**
 * Active user data
 *
 * @var array
 * @access public
 */		
	var $activeUser = null;

	
/**
 * Controller::beforeFilter() callback
 *
 * Handles global configuration, such as app and auth settings. Also
 * does some RequestHandler magic.
 *
 * @see Cake docs
 */
	function beforeFilter() {
		$this->_visitHistory = $this->Session->read('CORE.visitHistory');
		$this->_visitHistory[] = $this->here;
		$this->Session->write('CORE.visitHistory', $this->_visitHistory);
	
		// pull app settings
		$appSettings = Cache::read('core_app_settings');
		if (empty($appSettings)) {
			$appSettings = ClassRegistry::init('AppSetting')->find('all');
			// add tagless versions of the html tagged ones
			$tagless = array();
			foreach ($appSettings as $appSetting) {
				if ($appSetting['AppSetting']['html']) {
					$tagless[] = array(
						'AppSetting' => array(
							'name' => $appSetting['AppSetting']['name'].'_tagless',
							'value' => strip_tags($appSetting['AppSetting']['value'])
						)
					);							
				}
			}
			$appSettings = array_merge($appSettings, $tagless);
			$appSettings = Set::combine($appSettings, '{n}.AppSetting.name', '{n}.AppSetting.value');
			Cache::write('core_app_settings', $appSettings);
		}
		
		Configure::write('CORE.settings', $appSettings);
		$this->CORE = array(
			'version' => $this->_version,
			'settings' => $appSettings
		);
		
		$User = ClassRegistry::init('User');
		
		if ($this->Auth->user()) {
			// keep user available
			$this->activeUser = array_merge($this->Auth->user(), $this->Session->read('User'));
			
			// get latest alert
			$userGroups = Set::extract('/Group/id', $this->activeUser);
			$Alert = ClassRegistry::init('Alert');
						
			// get notifications count
			$newNotifications = $User->Notification->find('count', array(
				'conditions' => array(
					'Notification.user_id' => $this->Auth->user('id'),
					'Notification.read' => false
				),
				'contain' => false
			));
			
			$unread = $Alert->getUnreadAlerts($this->activeUser['User']['id'], $userGroups, false);
			
			$lastUnreadAlert = $Alert->find('first', array(
				'conditions' => array(
					'Alert.id' => $unread
				),
				'order' => 'Alert.created DESC'
			));
			if ($lastUnreadAlert) {
				$this->activeUser = array_merge($lastUnreadAlert, $this->activeUser);
			}
			$this->activeUser['User']['new_notifications'] = $newNotifications;
			$this->activeUser['User']['new_alerts'] = count($unread);
			
			// global allowed actions
			$this->Auth->allow('display');
		} else {
			$this->layout = 'public';
		}
		
		// use custom authentication (password encrypt/decrypt)
		$this->Auth->authenticate = $User;
		
		/* 
		json breaks if there's the time comment that debug adds (<!-- 0.0325 sec -->),
		not to mention the debugging info. you can still see debug info by going to it 
		directly (that is, not using ajax and using the default layout instead)
		*/
		if (($this->RequestHandler->isAjax() && $this->RequestHandler->ext == 'json') || $this->RequestHandler->ext == 'csv') {				
			Configure::write('debug', 0);
		}
		
		// set to log using this user (see LogBehavior)
		if (!$this->params['plugin'] && sizeof($this->uses) && $this->{$this->modelClass}->Behaviors->attached('Logable')) { 
			$this->{$this->modelClass}->setUserData($this->activeUser); 
		}
	}

/**
 * Authorizes a user to access an action based on ACLs
 *
 * @return boolean True if user can continue.
 */ 
	function isAuthorized() {
		if (!$this->activeUser) {
			return false;
		}
		
		$this->_setConditionalGroups();
		
		$model = 'Group';
		$foreign_key = $this->activeUser['Group']['id'];
		$userId = $this->activeUser['User']['id'];
		
		$mainAccess = $this->Acl->check(compact('model', 'foreign_key'), $this->Auth->action());
		CakeLog::write('auth', "User $userId of group $foreign_key allowed to go to ".$this->Auth->action().'? ['.($mainAccess) ? 'yes' : 'no'.']');
		
		$condAccess = false;
		// check for conditional group, which takes priority
		if (isset($this->activeUser['ConditionalGroup'])) {
			$foreign_key = $this->activeUser['ConditionalGroup']['id'];
			$condAccess = $this->Acl->check(compact('model', 'foreign_key'), $this->Auth->action());
			if ($condAccess) {
				CakeLog::write('auth', "User $userId of conditional group $foreign_key allowed to go to ".$this->Auth->action().'? ['.($condAccess) ? 'yes' : 'no'.']');
			}
		}
		
		return $mainAccess || $condAccess;
	}

/**
 * Controller::beforeRender() callback
 *
 * Sets globally needed variables for the views.
 *
 * @see Controller::beforeRender()
 */	
	function beforeRender() {
		$this->set('CORE', $this->CORE);	
		$this->set('activeUser', $this->activeUser);	
		$this->set('defaultSubmitOptions', $this->defaultSubmitOptions);
	}
	
/**
 * Allows simple session storage and manipulation for MultiSelectHelper and MultiSelectComponent
 *
 * @param string $action Action to take
 * @param string $data Comma delimited list of data
 * @access public
 */
	function multi_select_session($action = 'deselectAll', $data = '') {		
		// no access from anything other than the helper's functions
		if (!$this->RequestHandler->isAjax() || $this->RequestHandler->ext != 'json') {
			$this->cakeError('error404');
		}
		
		$this->autoRender = false;
		
		// call MultiSelect::$action
		$cache = $this->MultiSelect->{$action}(explode(',', $data));
			
		echo json_encode($cache);
		$this->_stop();
	}
	
/**
 * Converts POST'ed form data to a model conditions array, suitable for use in a Model::find() call.
 *
 * @param array $data POST'ed data organized by model and field
 * @param mixed $op A string containing an SQL comparison operator, or an array matching operators
 *        to fields
 * @param string $bool SQL boolean operator: AND, OR, XOR, etc.
 * @param boolean $exclusive If true, and $op is an array, fields not included in $op will not be
 *        included in the returned conditions
 * @return array An array of model conditions
 * @access public
 * @link http://book.cakephp.org/view/989/postConditions
 */
	function postConditions($data = array(), $op = null, $bool = 'AND', $exclusive = false) {
		if (!is_array($data) || empty($data)) {
			if (!empty($this->data)) {
				$data = $this->data;
			} else {
				return null;
			}
		}
		$registered = ClassRegistry::keys();
		$bools = array('and', 'or', 'not', 'and not', 'or not', 'xor', '||', '&&');
		$cond = array();

		if ($op === null) {
			$op = '';
		}
		
		$arrayOp = is_array($op);
		foreach ($data as $model => $fields) {
			if (is_array($fields)) {
				foreach ($fields as $field => $value) {
					if (is_array($value) && in_array(strtolower($field), $registered)) {
						$cond += (array)self::postConditions(array($field=>$value), $op, $bool, $exclusive);
					} else {
						// check for boolean keys
						if (in_array(strtolower($model), $bools)) {
							$key = $field;
						} else {
							$key = $model.'.'.$field;
						}
						
						// check for habtm [Publication][Publication][0] = 1
						if ($model == $field) {
							// should get PK
							$key = $model.'.id';
						}
						
						$fieldOp = $op;
						
						if ($arrayOp) {
							if (array_key_exists($key, $op)) {
								$fieldOp = $op[$key];
							} elseif (array_key_exists($field, $op)) {
								$fieldOp = $op[$field];
							} else {
								$fieldOp = false;
							}
						}
						if ($exclusive && $fieldOp === false) {
							continue;
						}
						$fieldOp = strtoupper(trim($fieldOp));
						if (is_array($value) || is_numeric($value)) {
							$fieldOp = '=';					
						}
						if ($fieldOp === 'LIKE') {
							$key = $key.' LIKE';
							$value = '%'.$value.'%';
						} elseif ($fieldOp && $fieldOp != '=') {
							$key = $key.' '.$fieldOp;
						}
						
						$cond[$key] = $value;
					}
				}
			}
		}
		if ($bool != null && strtoupper($bool) != 'AND') {
			$cond = array($bool => $cond);
		}
		
		return $cond;
	}	
			
/**
 * Sends an email
 *
 * If $from is not defined, it sends the email from the site instead, using
 * configured options (see AppSettings)
 *
 * #### Options:
 * 		integer $from The User id of the sender
 * 		mixed $to List of User ids to send to (can be one)
 * 		string $subject The subject line
 * 		string $template The template to load (view element)
 * 		string $layout The layout to load
 * @return boolean Success
 * @access protected
 */
	function _sendEmail($options = array()) {
		$this->Email->reset();
		$User =& ClassRegistry::init('User');
		$User->contain(array('Profile'));
		
		$default = array(
			'from' => null, 
			'to' => array(), 
			'subject' => '',
			'template' => 'default', 
			'layout' => 'default',
			'body' => null
		);
		
		$options = array_merge($default, $options);
		extract($options);
		
		$smtp = array(
			'port'=>'25',
			'timeout'=>'30',
			'host' => 'mail.rockharbor.org'/*,
			'username' => 'rh\core',
			'password' => 'c0R3!@R0cK5'*/
		);		
		
		$systemEmail = array(
			'Profile' => array(
				'name' => $this->CORE['settings']['site_name_tagless'],
				'primary_email' => $this->CORE['settings']['site_email']
			)
		);
		
		// set system defaults if no 'from' user
		if (!$from) {
			$from = $systemEmail;
		} else {
			$from = $User->read(null, $from);
		}
		
		$this->beforeRender();
		
		$this->Email->smtpOptions = $smtp;
		$this->Email->delivery = 'smtp';
		$this->Email->sendAs = 'html';
		$this->Email->layout = $layout;
		$this->Email->template = $template;
		
		// check if they just sent one user
		if (!is_array($to)) {
			$to = array($to);
		}
		
		$to = $User->find('all', array(
			'conditions' => array(
				'User.id' => $to,
				'User.active' => true
			),
			'contain' => array(
				'Profile'
			)
		));
		
		/*** need to add emails to queue ***/
		$bcc = array();
		foreach ($to as $toUser) {
			if (!empty($toUser['Profile']['primary_email']) && !empty($toUser['Profile']['name'])) {
				$bcc[] = $toUser['Profile']['name'].' <'.$toUser['Profile']['primary_email'].'>';
			}
		}
		$this->Email->from = $from['Profile']['name'].' <'.$from['Profile']['primary_email'].'>';
		
		if (Configure::read() > 0) {
			$this->Email->bcc = array('CORE DEBUG <'.$this->CORE['settings']['debug_email'].'>');
			$this->Email->to = $this->activeUser['Profile']['name'].' <'.$this->activeUser['Profile']['primary_email'].'>';
		} else {
			$this->Email->bcc = $bcc;
			$this->Email->to = $systemEmail['Profile']['name'].' <'.$systemEmail['Profile']['primary_email'].'>';
		}
		
		$this->Email->subject = $this->CORE['settings']['email_subject_prefix'].' '.$subject;
		
		$this->Email->_debug();
		
		if (!$this->Email->send($body)) {
			CakeLog::write('smtp', $this->Email->smtpError);
			return false;
		} else {
			return true;
		}
	}

/**
 * Creates a conditional group, if appropriate
 *
 * Conditional groups are things like Owner, Household Contact, Leader, etc. They are
 * created on a case by case basis depending on if the user qualifies. For example, if the
 * active user owns the record they are trying to edit, they are added to the Owner
 * conditional group. These groups are not persistent.
 *
 * @return void
 * @access protected
 */ 
	function _setConditionalGroups() {
		$Group = ClassRegistry::init('Group');
	
		if (isset($this->passedArgs['User'])) {
			$User = ClassRegistry::init('User');
			
			// check household contact
			if ($User->HouseholdMember->Household->isContactFor($this->activeUser['User']['id'], $this->passedArgs['User'])) {
				$this->activeUser['ConditionalGroup'] = reset($Group->findByName('Household Contact'));
			}
		
			// check owner
			if ($User->ownedBy($this->activeUser['User']['id'], $this->passedArgs['User'])) {
				$this->activeUser['ConditionalGroup'] = reset($Group->findByName('Owner'));
			}
		}
		
		// check leader
		if (isset($this->passedArgs['Involvement'])) {
			$Involvement = ClassRegistry::init('Involvement');
			if ($Involvement->isLeader($this->activeUser['User']['id'], $this->passedArgs['Involvement'])) {
				$this->activeUser['ConditionalGroup'] = reset($Group->findByName('Involvement Leader'));
			}
		}
		
		// check ministry manager
		if (isset($this->passedArgs['Ministry'])) {
			$Ministry = ClassRegistry::init('Ministry');
			if ($Ministry->isManager($this->activeUser['User']['id'], $this->passedArgs['Ministry'])) {
				$this->activeUser['ConditionalGroup'] = reset($Group->findByName('Ministry Manager'));
			}
		}
		
		// check campus manager
		if (isset($this->passedArgs['Campus'])) {
			$Campus = ClassRegistry::init('Campus');
			if ($Campus->isManager($this->activeUser['User']['id'], $this->passedArgs['Campus'])) {
				$this->activeUser['ConditionalGroup'] = reset($Group->findByName('Campus Manager'));
			}
		}
	}

/**
 * Auto-sets User named parameter for specific actions (passed as argument list)
 *
 * @return void
 * @access protected
 */ 
	function _editSelf() {
		$actions = func_get_args();
		
		if (in_array($this->action, $actions)) {
			if (!isset($this->passedArgs['User'])) {
				$this->passedArgs['User'] = $this->activeUser['User']['id'];
				$this->params['named']['User'] = $this->activeUser['User']['id'];
			}
		}		
	}
}
?>