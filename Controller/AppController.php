<?php
/**
 * App controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app
 */

App::uses('Controller', 'Controller');

/**
 * App Controller
 *
 * All controllers within the CORE app should extend this class.
 *
 * @package       core
 * @subpackage    core.app
 */
class AppController extends Controller {

	public $components = array(
		'Session',
		'Email',
		'DebugKit.Toolbar' => array(
			'panels' => array(
				'CoreDebugPanels.auth'
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
			'userScope' => array(
				'User.active' => true,
				'or' => array(
					'DATEDIFF(CURDATE(), Profile.birth_date)/365.25 >' => 12,
					'Profile.adult' => true
				)
			)
		),
		'Notifier',
		'QueueEmail.QueueEmail',
		'Security'
	);

/**
 * Application-wide helpers
 *
 * @var array
 */
	public $helpers = array(
		'Js' => array('Jquery'),
		'Session',
		'Text',
		'Media.Media',
		'Permission',
		'AssetCompress.AssetCompress'
	);

/**
 * Default callbacks for ajax submit buttons
 *
 * @var array
 */
	public $defaultSubmitOptions = array(
		'before' => 'CORE.beforeForm(event, XMLHttpRequest);',
		'complete' => 'CORE.completeForm(event, XMLHttpRequest, textStatus);',
		'success' => 'CORE.successForm(event, data, textStatus);',
		'evalScripts' =>  true
	);

/**
 * Empty page as Cake url
 *
 * @var array
 */
	public $emptyPage = array(
		'controller' => 'pages',
		'action' => 'display',
		'empty'
	);

/**
 * Failed authorization page as Cake url
 *
 * @var array
 */
	public $authPage = array(
		'controller' => 'pages',
		'action' => 'display',
		'auth_fail'
	);

/**
 * Active user data
 *
 * @var array
 */
	public $activeUser = null;

/**
 * Controller::beforeFilter() callback
 *
 * Handles global configuration, such as app and auth settings.
 *
 * @see Controller::beforeFilter()
 */
	public function beforeFilter() {
		if (isset($this->params['renderAs'])) {
			$this->autoLayout = true;
			$this->RequestHandler->renderAs($this, $this->params['renderAs']);
		}
		// add extra mappings
		// WORKAROUND: Firefox tries to open json instead of reading it, so use different headers
		$this->RequestHandler->setContent('json', 'text/plain');
		$this->RequestHandler->setContent('print', 'text/html');

		$User = ClassRegistry::init('User');

		if ($this->Auth->user() && $this->action !== 'logout') {
			// keep user available
			$this->activeUser = array_merge($this->Auth->user(), $this->Session->read('User'));
		} else {
			$this->layout = 'public';
		}

		// use custom authentication (password encrypt/decrypt)
		$this->Auth->authenticate = new User();

		$this->Security->blackHoleCallback = 'cakeError';

		// set to log using this user (see LogBehavior)
		if ((!isset($this->params['plugin']) || !$this->params['plugin']) && sizeof($this->uses) && isset($this->{$this->modelClass}->Behaviors) && $this->{$this->modelClass}->Behaviors->attached('Logable')) {
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
	public function isAuthorized($action = '', $params = array(), &$user = array()) {
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

		// main group
		$mainAccess = Core::acl($user['Group']['id'], $action);

		$condAccess = false;
		// check for conditional group
		if (!empty($user['ConditionalGroup'])) {
			foreach ($user['ConditionalGroup'] as $group) {
				$condAccess = Core::acl($group['Group']['id'], $action, 'conditional');
				if ($condAccess) {
					break;
				}
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
	public function beforeRender() {
		$this->defaultSubmitOptions['url'] = $this->here;

		$this->set('activeUser', $this->activeUser);
		$this->set('defaultSubmitOptions', $this->defaultSubmitOptions);

		// get ministry list
		if ($this->layout == 'default') {
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
							'Ministry.parent_id' => null,
							'Ministry.private' => false
						),
						'ChildMinistry' => array(
							'conditions' => array(
								'ChildMinistry.active' => true,
								'ChildMinistry.private' => false
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
				'cacher' => '+1 day'
			);
			$this->set('campusesMenu', $Campus->find('all', $options));
		}

		// increase security form timeout
		if ($this->Session->read('_Token')) {
			$token = unserialize($this->Session->read('_Token'));
			$token['expires'] = strtotime('+30 minutes');
			$this->Session->write('_Token', serialize($token));
		}
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
 * @link http://book.cakephp.org/view/989/postConditions
 */
	public function postConditions($data = array(), $op = null, $bool = 'AND', $exclusive = false) {
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

						if ($value !== '%%') {
							$cond[$key] = $value;
						}
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
 * If a URL contains more than one conditional group key (User, Involvement,
 * Ministry and Campus) the lower of the keys will be used to check permissions.
 * So, /involvements/view/Involvement:1/Ministry:5 would check permissions on
 * involvement 1, ignoring ministry 5. This prevents forging permissions by
 * entering a ministry you *are* managing in the URL of an involvement you *aren't*
 * in order to gain access.
 *
 * @param array $params The parameters to use in the check (default is passedArgs)
 * @param array $user The user to check
 * @return array Conditional groups
 */
	protected function _setConditionalGroups($params = array(), $user = array()) {
		$groups = array();
		$Group = ClassRegistry::init('Group');
		$Group->recursive = -1;

		if (isset($params['User'])) {
			$User = ClassRegistry::init('User');

			// check household contact
			if ($User->HouseholdMember->Household->isContactFor($user['User']['id'], $params['User'])) {
				$groups[] = $Group->findByName('Household Contact');
			}

			// check owner
			if ($User->ownedBy($user['User']['id'], $params['User'])) {
				$groups[] = $Group->findByName('Owner');
			}
		}

		// check leader
		if (isset($params['Involvement'])) {
			$Involvement = ClassRegistry::init('Involvement');
			if ($Involvement->isLeader($user['User']['id'], $params['Involvement'])) {
				$groups[] = $Group->findByName('Involvement Leader');
			}
			$involvement = $Involvement->read(array('id', 'ministry_id'), $params['Involvement']);
			$params['Ministry'] = $involvement['Involvement']['ministry_id'];
		}

		// check ministry manager
		if (isset($params['Ministry'])) {
			$Ministry = ClassRegistry::init('Ministry');
			if ($Ministry->isManager($user['User']['id'], $params['Ministry'])) {
				$groups[] = $Group->findByName('Ministry Manager');
			}
			$ministry = $Ministry->read(array('id', 'campus_id'), $params['Ministry']);
			$params['Campus'] = $ministry['Ministry']['campus_id'];
		}

		// check campus manager
		if (isset($params['Campus'])) {
			$Campus = ClassRegistry::init('Campus');
			if ($Campus->isManager($user['User']['id'], $params['Campus'])) {
				$groups[] = $Group->findByName('Campus Manager');
			}
		}

		return $groups;
	}

/**
 * Auto-sets User named parameter for specific actions (passed as argument list)
 *
 * @return void
 */
	protected function _editSelf() {
		$actions = func_get_args();

		if (in_array($this->action, $actions)) {
			if (!isset($this->passedArgs['User'])) {
				$this->passedArgs['User'] = $this->activeUser['User']['id'];
				$this->params['named']['User'] = $this->activeUser['User']['id'];
			}
		}
	}

/**
 * Extracts ids from multiple selection
 *
 * Errors are thrown if nothing is selected or a saved search is missing when
 * 'check all' is selected.
 *
 * {{{
 * if ($id) {
 *   // handle non MultiSelect requests
 *   $ids = array($id);
 * } else {
 *   // get selected ids
 *   $ids = $this->_extractIds($this->User, '/User/id');
 * }
 * }}}
 *
 * @param Model $model The model to use for searching
 * @param string $path A `Set::extract()`-compatible path
 * @return array Array of ids
 * @see MultiSelect.MultiSelect
 */
	protected function _extractIds($model = null, $path = '/User/id') {
		if (!isset($this->MultiSelect)) {
			trigger_error('MultiSelect component not loaded.', E_USER_NOTICE);
		}
		$ids = $this->MultiSelect->getSelected();
		if ($ids === 'all') {
			if (!$model) {
				trigger_error('Invalid model.', E_USER_NOTICE);
			}
			$search = $this->MultiSelect->getSearch();
			if (empty($search)) {
				$this->Session->setFlash('Please select some items before performing an action.', 'flash'.DS.'failure');
				$this->cakeError('invalidMultiSelectSelection');
			}
			$results = $model->find('all', $search);
			$ids = Set::extract($path, $results);
		}
		if (empty($ids)) {
			$this->Session->setFlash('Please select some items before performing an action.', 'flash'.DS.'failure');
			$this->cakeError('invalidMultiSelectSelection');
		}
		return $ids;
	}

}
