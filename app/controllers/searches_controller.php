<?php/** * Search controller class. * * @copyright     Copyright 2010, *ROCK*HARBOR * @link          http://rockharbor.org *ROCK*HARBOR * @package       core * @subpackage    core.app.controllers *//** * Searches Controller * * @package       core * @subpackage    core.app.controllers */class SearchesController extends AppController {/** * List of models this controller uses * * @var array */	var $uses = array('User','Ministry','Involvement');/** * Extra helpers for this controller * * @var array */	var $helpers = array('Formatting', 'Text', 'MultiSelect', 'SelectOptions', 'Media.Medium');/** * Extra components for this controller * * @var array */	var $components = array('FilterPagination', 'MultiSelect');/** * Model::beforeFilter() callback * * Used to override Acl permissions for this controller. * * @access private */	function beforeFilter() {		parent::beforeFilter();	}/** * Performs a simple search on Users, Ministries and Involvements * * ### Passed args: * - string $model The model to search. If none, searches all */	function index() {		$ministries = array();		$users = array();		$involvements = array();		$query = '';		if (isset($this->passedArgs['model'])) {			$restrictModel = $this->passedArgs['model'];		} else {			$restrictModel = false;		}		if (!empty($this->data)) {			$query = $this->data['Search']['query'];			$foreign_key = $this->activeUser['Group']['id'];			$model = 'Group';			$userSearch = array(				'User' => array(					'username' => $query,				),				'Profile' => array(					'first_name' => $query,					'last_name' => $query				)			);			// check access to results based on access to actions			$path = $this->Auth->actionPath.$this->name.'/user';			if ((!$restrictModel || $restrictModel == 'User') && $this->Acl->check(compact('model', 'foreign_key'), $path)) {				$users = $this->User->find('all', array(					'conditions' => $this->postConditions($userSearch, 'LIKE', 'OR'),					'contain' => array(						'Profile'					)				));			}			$ministrySearch = array(				'Ministry' => array(					'name' => $query,					'description' => $query				)			);			$path = $this->Auth->actionPath.$this->name.'/ministry';			if ((!$restrictModel || $restrictModel == 'Ministry') && $this->Acl->check(compact('model', 'foreign_key'), $path)) {				$ministries = $this->Ministry->find('all', array(					'conditions' => $this->postConditions($ministrySearch, 'LIKE', 'OR'),					'contain' => 'Group'				));			}			$involvementSearch = array(				'Involvement' => array(					'name' => $query,					'description' => $query				)			);			$path = $this->Auth->actionPath.$this->name.'/involvement';			if ((!$restrictModel || $restrictModel == 'Involvement') && $this->Acl->check(compact('model', 'foreign_key'), $path)) {				$involvements = $this->Involvement->find('all', array(					'conditions' => $this->postConditions($involvementSearch, 'LIKE', 'OR'),					'contain' => array(						'InvolvementType',						'Date',						'Group'					)				));			}		}		$this->set(compact('ministries', 'involvements', 'users', 'query'));	}/** * Performs an advanced search on Involvements */	function involvement() {		$results = array();		// at the very least, we want:		$contain = array('Ministry', 'InvolvementType');		$this->paginate = compact('contain');		if (!empty($this->data)) {			$operator = $this->data['Search']['operator'];			unset($this->data['Search']);			// remove blanks			$this->data = array_map('Set::filter', $this->data);			$contain = array_merge($contain, $this->Involvement->postContains($this->data));			$conditions = $this->postConditions($this->data, 'LIKE', $operator);			$this->data['Search']['operator'] = $operator;			$this->paginate = compact('conditions', 'contain', 'limit');		}		$results = $this->FilterPagination->paginate('Involvement');		$involvementTypes = $this->Involvement->InvolvementType->find('list');		$this->set(compact('results', 'involvementTypes'));		// pagination request		if (!empty($this->data) || isset($this->params['named']['page'])) {			// just render the results			$this->autoRender = false;			$this->viewPath = 'elements';			$this->render('search'.DS.'involvement_results');		}	}/** * Performs an advanced search on Ministries */	function ministry() {		$results = array();		// at the very least, we want:		$contain = array('Campus');		$this->paginate = compact('contain');		if (!empty($this->data)) {			$operator = $this->data['Search']['operator'];			unset($this->data['Search']);			// remove blanks			$this->data = array_map('Set::filter', $this->data);			$contain = array_merge($contain, $this->Ministry->postContains($this->data));			$conditions = $this->postConditions($this->data, 'LIKE', $operator);			$this->data['Search']['operator'] = $operator;			$this->paginate = compact('conditions', 'contain', 'limit');		}		$results = $this->FilterPagination->paginate('Ministry');		$campuses = $this->Ministry->Campus->find('list');		$this->set(compact('results', 'campuses'));		// pagination request		if (!empty($this->data) || isset($this->params['named']['page'])) {			// just render the results			$this->autoRender = false;			$this->viewPath = 'elements';			$this->render('search'.DS.'ministry_results');		}	}/** * Performs an advanced search on Users */	function user() {		$results = array();		// at the very least, we want:		$contain = array(			'Group',			'Address',			'Profile',			'HouseholdMember' => array(				'Household' => array(					'HouseholdContact'				)			),			'Image'		);		//$this->paginate = compact('contain');		if (!empty($this->data)) {			$options = $this->User->prepareSearch($this, $this->data);			$limit = 2;			// merge contains with defaults and just get ids (since this is just the filter stage)			foreach ($options['link'] as &$linkedModel) {				$linkedModel['fields'] = array('id');			}			$this->paginate = $options;			// first, search based on the linked parameters (which will filter)			$filteredUsers = $this->paginate();			// reset pagination			$this->paginate = array('contain' => $contain, 'conditions' => array('User.id' => Set::extract('/User/id', $filteredUsers)));			$this->MultiSelect->saveSearch($this->paginate);		}		$results = $this->FilterPagination->paginate();		$publications = $this->User->Publication->find('list');		$campuses = $this->User->Profile->Campus->find('list');		$regions = $this->User->Address->Zipcode->Region->find('list');		$classifications = $this->User->Profile->Classification->find('list');		$this->set('elementarySchools', $this->User->Profile->ElementarySchool->find('list', array(			'conditions' => array('type' => 'e')		)));		$this->set('middleSchools', $this->User->Profile->MiddleSchool->find('list', array(			'conditions' => array('type' => 'm')		)));		$this->set('highSchools', $this->User->Profile->HighSchool->find('list', array(			'conditions' => array('type' => 'h')		)));		$this->set('colleges', $this->User->Profile->College->find('list', array(			'conditions' => array('type' => 'c')		)));		$this->set(compact('results', 'publications', 'regions', 'classifications', 'campuses'));		// pagination request		if (!empty($this->data) || isset($this->params['named']['page'])) {			// just render the results			$this->autoRender = false;			$this->viewPath = 'elements';			$this->render('search'.DS.'user_results');		}	}}?>