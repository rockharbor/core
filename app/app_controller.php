<?php
/**
 * App controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app
 */

App::import('Vendor', array('file' => 'FirePHPCore/FirePHP.class.php'));

/**
 * App Controller
 *
 * All controllers within the CORE app should extend this class.
 *
 * @package       core
 * @subpackage    core.app
 */
class AppController extends Controller {

	var $components = array(
		'Session',
		'Email',
		'DebugKit.Toolbar' => array(
			'panels' => array(
				'CoreDebugPanels.errors',
				'CoreDebugPanels.visitHistory',
				'CoreDebugPanels.auth',
				'log' => false,
				'history' => false
			)
		),
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
				'action' => 'login',
				'plugin' => null
			),
			'logoutRedirect' => array(
				'controller' => 'users', 
				'action' => 'login'
			),
			'loginRedirect' => array(
				'controller' => 'profiles',
				'action' =>'view'
			),
			'userScope' => array('User.active' => true)
		),
		'Referee.Whistle' => array(
			'paths' => array(
				LISTENER_PATH
			),
			'listeners' => array(
				'DbLog',
				'Screen',
				'Email' => array(
					array(
						'levels' => E_ERROR
					),
					array(
						'levels' => E_WARNING
					)
				)
			)
		),
		'Notifier' => array(
			'saveData' => array(
				'type' => 'default'
			)
		),
		'QueueEmail.QueueEmail',
		'Security'
	);

/**
 * Application-wide helpers
 *
 * @var array
 */
	var $helpers = array(
		'Js' => array('Jquery'),
		'Session',
		'Text',
		'Media.Media',
		'Tree',
		'Permission'
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
 * Handles global configuration, such as app and auth settings.
 *
 * @see Controller::beforeFilter()
 */
	function beforeFilter() {
		if (isset($this->params['renderAs'])) {
			$this->RequestHandler->renderAs($this, $this->params['renderAs']);
		}
		// add extra mappings
		// WORKAROUND: Firefox tries to open json instead of reading it, so use different headers
		$this->RequestHandler->setContent('json', 'text/plain');
		$this->RequestHandler->setContent('print', 'text/html');
		
		$User = ClassRegistry::init('User');

		if ($this->Auth->user()) {
			// keep user available
			$this->activeUser = array_merge($this->Auth->user(), $this->Session->read('User'));

			// force redirect if they need to reset their password
			if ($this->activeUser['User']['reset_password'] && 
				!($this->name == 'Users' && ($this->action == 'edit' || $this->action == 'logout')) &&
				!isset($this->params['requested']) &&
				!$this->RequestHandler->isAjax()
			) {				
				$this->Session->setFlash('Your last password was automatically generated. Please reset it.');
				$this->redirect(array('controller' => 'users', 'action' => 'edit', 'User' => $this->Auth->user('id')));
			}
			// get latest alert
			$Alert = ClassRegistry::init('Alert');
			$unread = $Alert->getUnreadAlerts($this->activeUser['User']['id'], $this->activeUser['Group']['id'], false);
			$newAlerts = $Alert->find('all', array(
				'conditions' => array(
					'Alert.id' => $unread
				),
				'order' => 'Alert.created DESC',
				'limit' => 5
			));
			$this->activeUser['Alert'] = Set::extract('/Alert', $newAlerts);
		} else {
			$this->layout = 'public';
		}

		// use custom authentication (password encrypt/decrypt)
		$this->Auth->authenticate = new User();

		// cache permissions
		$this->Acl->Aro->Permission->Behaviors->attach('Cacher.cache', array('auto' => true));
		
		// set to log using this user (see LogBehavior)
		if (!$this->params['plugin'] && sizeof($this->uses) && $this->{$this->modelClass}->Behaviors->attached('Logable')) { 
			$this->{$this->modelClass}->setUserData($this->activeUser); 
		}
	}

/**
 * Authorizes a user to access an action based on ACLs
 *
 * @param string $action The action aco to check
 * @param array $params Parameters to use for checking conditional groups. Default
 *		params are the passedArgs
 * @param array $user The user to test. Default is the active user
	 * @return boolean True if user can continue.
 */ 
	function isAuthorized($action = '', $params = array(), &$user = array()) {
		if (!$this->activeUser && empty($user)) {
			return false;
		}

		if (empty($action)) {
			$action = $this->Auth->action();
		} else {
			$parsed = Router::parse($action);
			$action = Set::filter(array(Inflector::camelize($parsed['plugin']), Inflector::camelize($parsed['controller']), $parsed['action']));
			$action = implode('/', $action);
		}

		if (empty($user)) {
			$user =& $this->activeUser;
		}
		if (empty($params)) {
			$params = $this->passedArgs;
		}
		unset($user['ConditionalGroup']);
		$user['ConditionalGroup'] = $this->_setConditionalGroups($params, $user);
		$model = 'Group';
		$foreign_key = $user['Group']['id'];

		// main group
		$mainAccess = $this->Acl->check(compact('model', 'foreign_key'), $action);
		$userId = $user['User']['id'];
		$message = "User $userId of group $foreign_key allowed to access $action? [$mainAccess]";
		CakeLog::write('auth', $message);
		
		$condAccess = false;
		// check for conditional group
		if (!empty($user['ConditionalGroup'])) {
			$foreign_key = $user['ConditionalGroup']['id'];
			$condAccess = $this->Acl->check(compact('model', 'foreign_key'), $action);
			$message = "User $userId of group $foreign_key allowed to access $action? [$condAccess]";
			CakeLog::write('auth', $message);
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
		$this->set('activeUser', $this->activeUser);	
		$this->set('defaultSubmitOptions', $this->defaultSubmitOptions);

		// get ministry list
		$Campus = ClassRegistry::init('Campus');
		$Group = ClassRegistry::init('Group');
		$options = array(
			'fields' => array(
				'Campus.name'
			),
			'conditions' => array(
				'Campus.active' => true
			),
			'order' => 'Campus.id',
			'contain' => array(
				'Ministry' => array(
					'fields' => array(
						'Ministry.id',
						'Ministry.name'
					),
					'conditions' => array(
						'Ministry.active' => true,
						'Ministry.parent_id' => null
					),
					'ChildMinistry' => array(
						'conditions' => array(
							'ChildMinistry.active' => true,
						),
						'fields' => array(
							'ChildMinistry.id',
							'ChildMinistry.parent_id',
							'ChildMinistry.name'
						),
						'limit' => 5,
						'order' => 'ChildMinistry.name',
					),
					'order' => 'Ministry.name',
				)
			),
			'cache' => '+1 day'
		);
		if (!in_array($this->activeUser['Group']['id'], array_keys((array)$Group->findGroups(Core::read('general.private_group'), 'list', '>')))) {
			$options['contain']['Ministry']['conditions']['Ministry.private'] = false;
			$options['contain']['Ministry']['ChildMinistry']['conditions']['ChildMinistry.private'] = false;
		}
		$this->set('campusesMenu', $Campus->find('all', $options));
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
		unset($data['_Token']);
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
 * Creates a conditional group, if appropriate
 *
 * Conditional groups are things like Owner, Household Contact, Leader, etc. They are
 * created on a case by case basis depending on if the user qualifies. For example, if the
 * active user owns the record they are trying to edit, they are added to the Owner
 * conditional group. These groups are not persistent.
 *
 * @param array $params The parameters to use in the check (default is passedArgs)
 * @param array $user The user to check
 * @return array Conditional groups
 * @access protected
 */ 
	function _setConditionalGroups($params = array(), $user = array()) {
		$groups = array();
		$Group = ClassRegistry::init('Group');
		$Group->recursive = -1;
		
		if (isset($params['User'])) {
			$User = ClassRegistry::init('User');

			// check household contact
			if ($User->HouseholdMember->Household->isContactFor($user['User']['id'], $params['User'])) {
				$groups = reset($Group->findByName('Household Contact'));
			}
		
			// check owner
			if ($User->ownedBy($user['User']['id'], $params['User'])) {
				$groups = reset($Group->findByName('Owner'));
			}
		}
		
		// check leader
		if (isset($params['Involvement'])) {
			$Involvement = ClassRegistry::init('Involvement');
			if ($Involvement->isLeader($user['User']['id'], $params['Involvement'])) {
				$groups = reset($Group->findByName('Involvement Leader'));
			}
		}
		
		// check ministry manager
		if (isset($params['Ministry'])) {
			$Ministry = ClassRegistry::init('Ministry');
			if ($Ministry->isManager($user['User']['id'], $params['Ministry'])) {
				$groups = reset($Group->findByName('Ministry Manager'));
			}
		}
		
		// check campus manager
		if (isset($params['Campus'])) {
			$Campus = ClassRegistry::init('Campus');
			if ($Campus->isManager($user['User']['id'], $params['Campus'])) {
				$groups = reset($Group->findByName('Campus Manager'));
			}
		}

		return $groups;
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

/**
 * Forces the user to use SSL for this request
 *
 * @see SecurityComponent::blackHoleCallback
 */
	function _forceSSL() {
		$this->redirect('https://' . env('SERVER_NAME') . $this->here);
	}
}
?>