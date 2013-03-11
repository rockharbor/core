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
	public $uses = array('User','Ministry','Involvement');

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	public $helpers = array('Formatting', 'Text', 'MultiSelect.MultiSelect', 'SelectOptions', 'Media.Media');

/**
 * Extra components for this controller
 *
 * @var array
 */
	public $components = array(
		'FilterPagination',
		'MultiSelect.MultiSelect',
		'Security' => array(
			'enabled' => false
		)
	);

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 */
	public function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * Performs a simple search on Users, Ministries and Involvements
 *
 * ### Passed args:
 * - string $model The model to search. If none, searches all
 */
	public function index() {
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

		if (isset($this->data['Search']['q'])) {
			$this->params['url']['q'] = $this->data['Search']['q'];
			unset($this->data['Search']['q']);
		}

		$_default = array(
			'Search' => array(
				'query' => $this->params['url']['q'],
				'private' => $private,
				'active' => !$inactive,
				'previous' => 0
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
		if ($search['previous']) {
			unset($search['previous']);
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

		if (!empty($this->data['Search']['query'])) {
			$query = $search['query'];
			// check access to results based on access to actions
			if ((!$restrictModel || $restrictModel == 'User') && $this->isAuthorized('searches/user')) {
				$conditions = array(
					$this->User->scopeConditions($search)
				);
				$exp = explode(' ', $query);
				if (count($exp) > 1) {
					$firstname = array_shift($exp);
					$lastname = implode(' ', $exp);
					$conditions[] = array(
						'Profile.first_name LIKE' => $firstname.'%',
						'Profile.last_name LIKE' => $lastname.'%'
					);
				} else {
					$conditions['or'] = array(
						'User.username LIKE' => '%'.$query.'%',
						'Profile.first_name LIKE' => '%'.$query.'%',
						'Profile.last_name LIKE' => '%'.$query.'%',
					);
				}

				$options = array(
					'fields' => array(
						'id', 'username', 'active', 'flagged'
					),
					'conditions' => $conditions,
					'link' => array(
						'Profile' => array(
							'fields' => array(
								'name', 'primary_email',
								'alternate_email_1', 'alternate_email_2',
								'cell_phone',
								'background_check_complete'
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
						'id', 'name', 'description', 'active', 'private', 'previous'
					),
					'conditions' => array(
						$this->Involvement->scopeConditions($search),
						'Involvement.name LIKE' => '%'.$query.'%'
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
						),
						'Image'
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
						'Ministry.name LIKE' => '%'.$query.'%'
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
					'limit' => 6
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
	public function simple($model = null, $element = null, $filter = '') {
		$results = array();
		$searchRan = false;

		if (!empty($this->data)) {
			// reset multiselect
			unset($this->passedArgs['mstoken']);
			$this->MultiSelect->startup();

			// create conditions and contain
			$options = (array)$this->{$model}->postOptions($this->data) + array('contain' => array());;
			$options = array(
				'conditions' => $this->postConditions($this->data, 'LIKE'),
				'link' => $options['contain']
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
	public function involvement() {
		if (isset($this->params['url']['q'])) {
			$this->data['Involvement']['name'] = $this->params['url']['q'];
			unset($this->params['url']['q']);
		}

		$private = $this->Involvement->Roster->User->Group->canSeePrivate($this->activeUser['Group']['id']);
		$inactive = $private;
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
			$_default = array(
				'Involvement' => array(
					'private' => $private ? 1 : 0,
					'inactive' => $inactive ? 1 : 0,
					'previous' => 0
				),
				'Distance' => array(
					'distance_from' => null
				)
			);
			$this->data = $search = Set::merge($_default, $this->data);

			$dist = $search['Distance'];
			unset($search['Distance']);
			if (!empty($dist['distance_from'])) {
				$coords = $this->Involvement->Address->geoCoordinates($dist['distance_from']);
				$this->Involvement->Address->virtualFields['distance'] = $this->Involvement->Address->distance($coords['lat'], $coords['lng']);

				// get addresses within distance requirements
				$distancedAddresses = $this->Involvement->Address->find('all', array(
					'fields' => array(
						'id'
					),
					'conditions' => array(
						$this->Involvement->Address->getVirtualField('distance').' <= ' => (int)$dist['distance'],
						'model' => 'Involvement'
					)
				));
				$link['Address'] = array();
				$addresses = array_values(Set::extract('/Address/id', $distancedAddresses));
				if (empty($addresses)) {
					// no addresses found, so no results should be found
					$addresses = 0;
				}
				$search['Address']['id'] = $addresses;
			}

			if ($search['Involvement']['private']) {
				$search['Involvement']['private'] = array(0,1);
			} else {
				$search['Involvement']['private'] = 0;
			}
			if ($search['Involvement']['inactive']) {
				$search['Involvement']['active'] = array(0,1);
			} else {
				$search['Involvement']['active'] = 1;
			}
			unset($search['Involvement']['inactive']);
			if ($search['Involvement']['previous']) {
				unset($search['Involvement']['previous']);
			}

			$search = Set::filter($search);
			$options = (array)$this->Involvement->postOptions($search) + array('contain' => array());
			$link = array_merge_recursive($link, $options['contain']);
			$conditions = $this->postConditions($search, 'LIKE');

			$this->paginate = compact('conditions', 'link', 'limit');
		}

		$results = $this->FilterPagination->paginate('Involvement');
		$involvementTypes = $this->Involvement->InvolvementType->find('list');
		$this->set(compact('results', 'involvementTypes', 'private', 'inactive'));
	}

/**
 * Performs an advanced search on Ministries
 */
	public function ministry() {
		if (isset($this->params['url']['q'])) {
			$this->data['Ministry']['name'] = $this->params['url']['q'];
			unset($this->params['url']['q']);
		}

		$private = $this->Involvement->Roster->User->Group->canSeePrivate($this->activeUser['Group']['id']);
		$inactive = $private;
		$results = array();

		// at the very least, we want:
		$contain = array('Campus');
		$this->paginate = compact('contain');

		if (!empty($this->data)) {
			$_default = array(
				'Ministry' => array(
					'private' => $private ? 1 : 0,
					'inactive' => $inactive ? 1 : 0
				)
			);
			$this->data = $search = Set::merge($_default, $this->data);

			if ($search['Ministry']['private']) {
				$search['Ministry']['private'] = array(0,1);
			}
			if ($search['Ministry']['inactive']) {
				$search['Ministry']['active'] = array(0,1);
			} else {
				$search['Ministry']['active'] = 1;
			}
			unset($search['Ministry']['inactive']);

			$options = (array)$this->Ministry->postOptions($search) + array('contain' => array());
			$contain = array_merge_recursive($contain, $options['contain']);
			$conditions = $this->postConditions($search, 'LIKE');

			$this->paginate = compact('conditions', 'contain', 'limit');
		}

		$results = $this->FilterPagination->paginate('Ministry');
		$campuses = $this->Ministry->Campus->find('list');
		$this->set(compact('results', 'campuses', 'inactive', 'private'));
	}

/**
 * Performs an advanced search on Users
 */
	public function user() {
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

			// minimal containment needed for multiselect actions
			$search = $this->paginate;
			unset($search['contain']);
			$search['link'] = array('Profile' => array(
				'fields' => array('id')
			));
			$this->MultiSelect->saveSearch($search);
		}

		$results = $this->FilterPagination->paginate();
		$campuses = $this->User->Profile->Campus->find('list');
		$regions = $this->User->Address->Zipcode->Region->find('list');
		$classifications = $this->User->Profile->Classification->find('list');
		$this->set('elementarySchools', $this->User->Profile->ElementarySchool->find('list'));
		$this->set('middleSchools', $this->User->Profile->MiddleSchool->find('list'));
		$this->set('highSchools', $this->User->Profile->HighSchool->find('list'));
		$this->set('colleges', $this->User->Profile->College->find('list'));
		$this->set('groups', $this->User->Group->find('list', array(
			'conditions' => array(
				'Group.conditional' => false
			)
		)));
		$this->set(compact('results', 'regions', 'classifications', 'campuses', 'groups'));
	}
}
