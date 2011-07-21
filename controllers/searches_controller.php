<?php
/**
 * Search controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Searches Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class SearchesController extends AppController {

/**
 * List of models this controller uses
 *
 * @var array
 */
	var $uses = array('User','Ministry','Involvement');

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array('Formatting', 'Text', 'MultiSelect.MultiSelect', 'SelectOptions', 'Media.Media');

/**
 * Extra components for this controller
 *
 * @var array
 */
	var $components = array('FilterPagination', 'MultiSelect.MultiSelect');

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */
	function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * Performs a simple search on Users, Ministries and Involvements
 *
 * ### Passed args:
 * - string $model The model to search. If none, searches all
 */
	function index() {
		$private = $this->Involvement->Roster->User->Group->canSeePrivate($this->activeUser['Group']['id']);
		$inactive = $private;
		$campuses = $this->Ministry->Campus->find('list');
		$ministries = array();
		$users = array();
		$involvements = array();
		$query = '';
		
		if (empty($this->data['Search']['Campus']['id'])) {
			$this->data['Search']['Campus']['id'] = array_keys($campuses);
		}

		$_default = array(
			'Search' => array(
				'query' => '',
				'private' => $private,
				'active' => !$inactive,
				'passed' => 0
			)
		);
		$this->data = Set::merge($_default, $this->data);
		$search = $this->data['Search'];
		if ($search['private']) {
			$search['private'] = array(0,1);
		}
		if (!$search['active']) {
			$search['active'] = array(0,1);
		}
		if ($search['passed']) {
			unset($search['passed']);
		}
		if (!empty($search['Campus'])) {
			$search['Campus.id'] = $search['Campus']['id'];
			unset($search['Campus']);
		}
		if (!empty($search['Ministry'])) {
			$search['Ministry.id'] = $search['Ministry']['id'];
			unset($search['Ministry']);
		}

		if (isset($this->passedArgs['model'])) {
			$restrictModel = $this->passedArgs['model'];
		} else {
			$restrictModel = false;
		}

		if (!empty($this->data)) {
			$query = explode(' ', $search['query']);
			foreach ($query as &$word) {
				$word .= '*';
				$word = trim($word);
			}			
			$query = implode(' ', $query);
			// check access to results based on access to actions
			if ((!$restrictModel || $restrictModel == 'User') && $this->isAuthorized('searches/user')) {
				$options = array(
					'fields' => array(
						'id', 'username', 'active', 'flagged'
					),
					'conditions' => array(
						$this->User->scopeConditions($search),
						'or' => array(
							$this->User->parseCriteria(array('simple_fulltext' => $query)),
							$this->User->Profile->parseCriteria(array('simple_fulltext' => $query)),
						)
					),
					'link' => array(
						'Profile' => array(
							'fields' => array(
								'name', 'primary_email',
								'alternate_email_1', 'alternate_email_2',
								'cell_phone'
							),
							'Campus' => array(
								'fields' => array('id', 'name')
							)
						)						
					),
					'contain' => array(
						'Image'						
					),
					'limit' => 9,
					'group' => 'User.id'
				);
				if (!empty($search['Ministry.id'])) {
					// this is a very *slow* join, so only use it if we need to
					$options['link']['Roster'] = array(
						 'Involvement' => array(
							  'Ministry'
						 )
					);
				}
				$users = $this->User->find('all', $options);
			}
			if ((!$restrictModel || $restrictModel == 'Involvement') && $this->isAuthorized('searches/involvement')) {
				$involvements = $this->Involvement->find('all', array(
					'fields' => array(
						'id', 'name', 'description', 'active', 'private', 'passed'
					),
					'conditions' => array(
						$this->Involvement->scopeConditions($search),
						$this->Involvement->parseCriteria(array('simple_fulltext' => $query))
					),
					'link' => array(
						'Ministry' => array(
							'fields' => array('id', 'name'),
							'Campus' => array(
								'fields' => array('id', 'name')
							),
							'ParentMinistry' => array(
								'fields' => array('id', 'name')
							)
						)
					),
					'limit' => 4
				));
				foreach ($involvements as &$involvement) {
					$involvement['dates'] = $this->Involvement->Date->generateDates($involvement['Involvement']['id'], array('limit' => 1));
				}
			}

			if ((!$restrictModel || $restrictModel == 'Ministry') && $this->isAuthorized('searches/ministry')) {
				$ministries = $this->Ministry->find('all', array(
					'fields' => array(
						'id', 'name', 'description', 'active', 'private'
					),
					'conditions' =>  array(
						$this->Ministry->scopeConditions($search),
						$this->Ministry->parseCriteria(array('simple_fulltext' => $query))
					),
					'link' => array(
						'Image',
						'Campus' => array(
							'fields' => array('id', 'name')
						),
						'ParentMinistry' => array(
							'fields' => array('id', 'name')
						)
					),
					'limit' => 5
				));
			}
		}

		$this->set(compact('ministries', 'involvements', 'users', 'campuses', 'private', 'inactive'));
	}

/**
 *
 * Anything after the first 3 arguments is considered a variable to be inserted
 * into the search filter (0-based index). These allow complex search filters
 * with minimal passed data
 *
 * ### Example:
 *
 * `/searches/simple/User/user_actions/notLeaderOf/Involvement/23`
 *
 * Uses the searchFilter `notLeaderOf` defined on the `User` model, and replaces
 * `:0:` with `Involvement` and `:1:` with `23`. It also loads the `user_actions`
 * element into the last column on the table that is used to perform actions on
 * found data.
 *
 * @param string $model The name of the model to search
 * @param string $element The element that creats the links/button actions
 *   to perform on results
 * @param string $filter A filter to use, as defined by $Model->searchFilter
 * @return array
 * @see Search.Searchable
 */
	function simple($model = null, $element = null, $filter = '') {
		$results = array();
		$searchRan = false;

		if (!empty($this->data)) {
			// reset multiselect
			unset($this->passedArgs['mstoken']);
			$this->MultiSelect->startup();
			
			// create conditions and contain
			$options = array(
				'conditions' => $this->postConditions($this->data, 'LIKE'),
				'link' => $this->{$model}->postContains($this->data)
			);

			if (!empty($filter) && isset($this->{$model}->searchFilter[$filter])) {
				/**
				 * Recursively runs an array through String::insert
				 *
				 * @param array $input The array
				 * @param array $args The insert values
				 * @return array
				 * @see String::insert()
				 */
				$string_insert_recursive = function ($input, $args) use (&$string_insert_recursive) {
					foreach ($input as &$value) {
						if (is_array($value)) {
							$value = $string_insert_recursive($value, $args);
						} elseif ($value !== null) {
							$value = String::insert($value, $args, array('after' => ':'));
						}
					}
					return $input;
				};
				$filters = $string_insert_recursive($this->{$model}->searchFilter[$filter], array_slice(func_get_args(), 3));
				$options = Set::merge($options, $filters);
			}
			$this->paginate = $options;
			$searchRan = true;
		}

		$results = $this->FilterPagination->paginate($model);

		$named = $this->passedArgs;
		$this->set(compact('results', 'searchRan', 'model', 'element', 'named'));
	}

/**
 * Performs an advanced search on Involvements
 */
	function involvement() {
		$results = array();

		// at the very least, we want:
		$link = array(
			'Ministry' => array(
				'Campus'
			), 
			'InvolvementType'
		);
		$this->paginate = compact('link');

		if (!empty($this->data)) {
			$operator = $this->data['Search']['operator'];
			unset($this->data['Search']);

			// remove blanks
			$this->data = array_map('Set::filter', $this->data);

			$link = array_merge_recursive($link, $this->Involvement->postContains($this->data));
			$conditions = $this->postConditions($this->data, 'LIKE', $operator);

			$this->data['Search']['operator'] = $operator;

			$this->paginate = compact('conditions', 'link', 'limit');
		}

		$results = $this->FilterPagination->paginate('Involvement');
		$involvementTypes = $this->Involvement->InvolvementType->find('list');
		$this->set(compact('results', 'involvementTypes'));

		// pagination request
		if (!empty($this->data) || isset($this->params['named']['page'])) {
			// just render the results
			$this->autoRender = false;
			$this->viewPath = 'elements';
			$this->render('search'.DS.'involvement_results');
		}
	}

/**
 * Performs an advanced search on Ministries
 */
	function ministry() {
		$results = array();

		// at the very least, we want:
		$contain = array('Campus');
		$this->paginate = compact('contain');

		if (!empty($this->data)) {
			$operator = $this->data['Search']['operator'];
			unset($this->data['Search']);

			// remove blanks
			$this->data = array_map('Set::filter', $this->data);

			$contain = array_merge($contain, $this->Ministry->postContains($this->data));
			$conditions = $this->postConditions($this->data, 'LIKE', $operator);

			$this->data['Search']['operator'] = $operator;

			$this->paginate = compact('conditions', 'contain', 'limit');
		}

		$results = $this->FilterPagination->paginate('Ministry');
		$campuses = $this->Ministry->Campus->find('list');
		$this->set(compact('results', 'campuses'));

		// pagination request
		if (!empty($this->data) || isset($this->params['named']['page'])) {
			// just render the results
			$this->autoRender = false;
			$this->viewPath = 'elements';
			$this->render('search'.DS.'ministry_results');
		}
	}

/**
 * Performs an advanced search on Users
 */
	function user() {
		$results = array();

		// at the very least, we want:
		$contain = array(
			'Group',
			'ActiveAddress',
			'Profile',
			'HouseholdMember' => array(
				'Household' => array(
					'HouseholdContact' => array(
						'Profile',
						'ActiveAddress'
					)
				)
			),
			'Image'
		);

		if (!empty($this->data)) {
			$options = $this->User->prepareSearch($this, $this->data);
			
			// cache the find all query, since it will remain the same during pagination
			$options['cache'] = '+5 minutes';

			// first, search based on the linked parameters (which will filter)
			$filteredUsers = $this->User->find('all', $options);
			// reset pagination
			$this->paginate = array(
				'contain' => $contain, 
				'conditions' => array('User.id' => Set::extract('/User/id', $filteredUsers)),
				'order' => 'Profile.first_name ASC, Profile.last_name ASC'
			);

			$this->MultiSelect->saveSearch($this->paginate);
		}

		$results = $this->FilterPagination->paginate();
		$publications = $this->User->Publication->find('list');
		$campuses = $this->User->Profile->Campus->find('list');
		$regions = $this->User->Address->Zipcode->Region->find('list');
		$classifications = $this->User->Profile->Classification->find('list');
		$this->set('elementarySchools', $this->User->Profile->ElementarySchool->find('list'));
		$this->set('middleSchools', $this->User->Profile->MiddleSchool->find('list'));
		$this->set('highSchools', $this->User->Profile->HighSchool->find('list'));
		$this->set('colleges', $this->User->Profile->College->find('list'));
		$this->set(compact('results', 'publications', 'regions', 'classifications', 'campuses'));

		// pagination request
		if (!empty($this->data) || isset($this->params['named']['page'])) {
			// just render the results
			$this->autoRender = false;
			$this->viewPath = 'elements';
			$this->render('search'.DS.'user_results');
		}
	}
}
?>