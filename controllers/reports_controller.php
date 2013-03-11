<?php
/**
 * Report controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Reports Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class ReportsController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	public $name = 'Reports';

/**
 * List of models this controller uses
 *
 * @var string
 */
	public $uses = array('User', 'Roster', 'Ministry', 'Involvement', 'Campus', 'Payment');

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	public $helpers = array(
		'GoogleMap',
		'Media.Media',
		'Report',
		'Charts.Charts' => array('Charts.GoogleStatic'),
		'Formatting',
		'MultiSelect.MultiSelect'
	);

/**
 * Extra components for this controller
 *
 * @var array
 */
	public $components = array(
		'MultiSelect.MultiSelect',
		'FilterPagination'
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
 * Reports home page
 */
	public function index() {
		$campuses = $this->Campus->find('list');
		$ministries = $this->Ministry->generatetreelist();

		$conditions = array();
		$involvedUsers = array();

		if (empty($this->data)) {
			$this->data = array(
				'Ministry' => array(
					'campus_id' => null,
					'id' => null
				),
				'Involvement' => array(
					'previous' => 'current'
				)
			);
		}

		if (!empty($this->data)) {
			if (!empty($this->data['Ministry']['campus_id'])) {
				// campus takes precedence
				$involvedUsers = $this->Campus->getInvolved($this->data['Ministry']['campus_id'], true);
				$conditions = array(
					'Ministry.campus_id' => $this->data['Ministry']['campus_id']
				);
				$this->data['Ministry']['id'] = null;
			} else if (!empty($this->data['Ministry']['id'])) {
				$involvedUsers = $this->Ministry->getInvolved($this->data['Ministry']['id'], true);
				$conditions = array(
					'or' => array(
						'Ministry.id' => $this->data['Ministry']['id'],
						'Ministry.parent_id' => $this->data['Ministry']['id']
					)
				);
			} else {
				// blank search
				$involvedUsers = $this->Campus->getInvolved(array_keys($campuses), true);
			}
		}

		$ministryCounts = array();
		$filteredMinistries = $this->Ministry->find('list', array(
			'conditions' => $conditions
		));
		$ministryCounts['total'] = count($filteredMinistries);
		$ministryCounts['active'] = $this->Ministry->find('count', array(
			'conditions' => array_merge($conditions, array('Ministry.active' => true))
		));
		$ministryCounts['private'] = $this->Ministry->find('count', array(
			'conditions' => array_merge($conditions, array('Ministry.private' => true, 'Ministry.active' => true))
		));

		$userCounts = array();
		$userCounts['total'] = $this->User->find('count');
		$activeUsers = $this->User->find('list', array(
			'conditions' => array(
				'User.active' => true
			)
		));
		$userCounts['active'] = count($activeUsers);

		$userCounts['involved'] = count($involvedUsers);
		$userCounts['logged_in'] = $this->User->find('count', array(
			'conditions' => array(
				'User.last_logged_in >' => date('Y-m-d 00:00:00', strtotime('now'))
			),
		));

		$filteredMinistries = array_flip($filteredMinistries);
		$involvementTypes = $this->Involvement->InvolvementType->find('list');
		$ds = $this->Involvement->getDatasource();
		$involvementCounts = array();
		foreach ($involvementTypes as $id => $type) {
			$options = array(
				'conditions' => array(
					'Involvement.involvement_type_id' => $id,
					'Involvement.ministry_id' => $filteredMinistries
				)
			);
			switch ($this->data['Involvement']['previous']) {
				case 'current':
					$options['conditions'][] = $ds->expression('NOT ('.$this->Involvement->getVirtualField('previous').')');
				break;
				case 'previous':
					$options['conditions'][] = $ds->expression('('.$this->Involvement->getVirtualField('previous').')');
				break;
			}

			$involvementCounts[$type]['total'] = $this->Involvement->find('count', $options);

			$options['conditions']['Involvement.active'] = true;
			$involvementCounts[$type]['active'] = $this->Involvement->find('count', $options);

			$options['contain'] = array('Involvement');
			$involvementCounts[$type]['leaders'] = $this->Involvement->Leader->find('count', $options);

			$involved = $this->Roster->find('all', array(
				'fields' => array(
					'Roster.id'
				),
				'conditions' => array(
					'Involvement.involvement_type_id' => $id,
					'Involvement.ministry_id' => $filteredMinistries,
					$ds->expression('NOT ('.$this->Involvement->getVirtualField('previous').')')
				),
				'group' => 'Roster.user_id',
				'contain' => array(
					'Involvement'
				)
			));
			$involvementCounts[$type]['involved'] = count($involved);
		}

		$this->set(compact('campuses', 'ministries', 'involvementTypes', 'userCounts', 'ministryCounts', 'involvementCounts'));
	}

/**
 * Payments report
 */
	public function payments() {
		$campuses = $this->Campus->find('list');
		$ministries = $this->Ministry->generatetreelist();
		$paymentTypes = $this->Payment->PaymentType->find('all');
		$paymentTypeTypes = $this->Payment->PaymentType->types;

		$rosterConditions = array();
		$conditions = array(
			'contain' => array(
				'PaymentType' => array(
					'fields' => array('id', 'name', 'type')
				),
				'Payer' => array(
					'Profile' => array(
						'fields' => array('id', 'name', 'user_id')
					)
				),
				'User' => array(
					'Profile' => array(
						'fields' => array('id', 'name', 'user_id')
					)
				),
				'Roster' => array(
					'fields' => array(
						'id', 'involvement_id', 'payment_option_id'
					),
					'Involvement' => array(
						'fields' => array('id', 'name')
					),
					'PaymentOption' => array(
						'fields' => array('id', 'account_code')
					)
				)
			)
		);
		if (!empty($this->data)) {
			$involvements = array();
			if (!empty($this->data['Involvement']['name'])) {
				// campus takes precedence
				$involvements = $this->Involvement->find('list', array(
					'conditions' => array(
						'Involvement.name LIKE' => '%'.$this->data['Involvement']['name'].'%'
					)
				));
			} else if (!empty($this->data['Ministry']['id'])) {
				$involvements = $this->Involvement->find('list', array(
					'conditions' => array(
						'Involvement.ministry_id' => $this->data['Ministry']['id']
					)
				));
				$this->data['Campus']['id'] = null;
				$this->data['Involvement']['name'] = null;
			} else if (!empty($this->data['Campus']['id'])) {
				$involvements = $this->Involvement->find('list', array(
					'conditions' => array(
						'Ministry.campus_id' => $this->data['Campus']['id']
					),
					'contain' => array(
						'Ministry'
					)
				));
				$this->data['Ministry']['id'] = null;
				$this->data['Involvement']['name'] = null;
			}

			if (!empty($involvements)) {
				$rosterConditions['conditions'] = array(
					'Roster.involvement_id' => array_keys($involvements)
				);
			}

			if (!empty($this->data['PaymentType']['id'])) {
				$conditions['conditions']['PaymentType.id'] = $this->data['PaymentType']['id'];
			}
			if (isset($this->data['PaymentType']) && isset($this->data['PaymentType']['type']) && $this->data['PaymentType']['type'] !== '') {
				$conditions['conditions']['PaymentType.type'] = (string)$this->data['PaymentType']['type'];
			}
			if (!empty($this->data['Payment']['start_date']) && !empty($this->data['Payment']['end_date'])) {
				$conditions['conditions']['Payment.created BETWEEN ? AND ?'] = array(
					date('Y-m-d', strtotime($this->data['Payment']['start_date'])),
					date('Y-m-d', strtotime($this->data['Payment']['end_date']))
				);
			}
			if (!empty($this->data['PaymentOption']['account_code'])) {
				$rosterConditions['conditions']['PaymentOption.account_code LIKE'] = '%'.$this->data['PaymentOption']['account_code'].'%';
				$rosterConditions['link'] = array(
					'PaymentOption'
				);
			}
		}

		$rosters = $this->Roster->find('list', $rosterConditions);

		$this->paginate = array_merge_recursive($conditions, array(
			'conditions' => array(
					'Payment.roster_id' => array_keys($rosters)
			))
		);
		$this->MultiSelect->saveSearch($this->paginate);
		$this->FilterPagination->startEmpty = false;
		$payments = $this->FilterPagination->paginate('Payment');

		$this->set(compact('ministries', 'campuses', 'paymentTypes', 'paymentTypeTypes', 'payments'));
	}

/**
 * Exports a saved search (from MultiSelectComponent) as a report
 *
 * If the extension is 'csv', set View::title_for_layout to set the name of the
 * csv. Data should be sent in an `Export` array formatted based on the
 * current model's contain format.
 *
 * @param string $model The model we're searching / exporting data from
 * @param string $uid The MultiSelect cache key to get results from
 * @see MultiSelectComponent::getSearch();
 */
	public function export($model) {
		// persist selected items through POST requests
		$this->here .= '/mspersist:1';

		if (!empty($this->data)) {
			$options = array();
			if ($this->data['Export']['type'] == 'csv') {
				$this->set('title_for_layout', strtolower($model).'-search-export');
				$options['attachment'] = $this->viewVars['title_for_layout'].'.csv';
			}
			// set render path (which sets response type)
			$this->RequestHandler->renderAs($this, $this->data['Export']['type']);
			$this->RequestHandler->respondAs($this->data['Export']['type'], $options);
			$aliases = $this->data['Export']['header_aliases'];
			$squashed = $this->data['Export']['squashed_fields'];
			$multiples = $this->data['Export']['multiple_records'];
			unset($this->data['Export']['type']);
			unset($this->data['Export']['header_aliases']);
			unset($this->data['Export']['squashed_fields']);
			unset($this->data['Export']['multiple_records']);

			$pk = $this->{$model}->primaryKey;

			$selected = $this->_extractIds($this->{$model}, "/$model/$pk");

			// add to field list if contain or link is restricting them
			$options = $this->{$model}->postOptions($this->data['Export']);
			$options['conditions']["$model.$pk"] = $selected;

			$results = $this->{$model}->find('all', $options);

			$this->set('models', $this->data['Export']);
			$this->set(compact('results', 'aliases', 'squashed', 'multiples'));
		}

		$this->set(compact('uid', 'model'));
	}

/**
 * Shows a map for an involvement
 *
 * ### Passed args:
 * - `Involvement` The Involvement id
 *
 * @param string $involvementId The name of the model to search
 */
	public function involvement_map() {
		if (!isset($this->passedArgs['Involvement'])) {
			$this->cakeError('error404');
		}

		$results = $this->Involvement->find('all', array(
			'conditions' => array(
				'Involvement.id' => $this->passedArgs['Involvement']
			),
			'contain' => array(
				'Address'
			)
		));

		$this->set(compact('results'));
	}

/**
 * Shows a map for a user or a group of users
 *
 * If a User id is passed through the passed args, it will be the sole user
 * placed on the map. Otherwise, it looks for a multi-select search and pulls
 * all the users that qualify in that search.
 *
 * ### Passed args:
 * - `User` The User id (optional)
 *
 * @param string $model The name of the model to search (User or related model)
 * @param string $uid The multi-select id
 */
	public function user_map($model = 'User', $uid = null) {
		$search = $this->MultiSelect->getSearch($uid);
		$selected = $this->MultiSelect->getSelected($uid);

		if (empty($model) || (empty($search) && !isset($this->passedArgs[$model]))) {
			$this->cakeError('error404');
		}

		if (isset($this->passedArgs[$model])) {
			$search = array();
			$selected = $this->passedArgs[$model];
		}
		if (!empty($selected)) {
			$search['conditions'][$model.'.id'] = $selected;
		}

		// find users
		$results = $this->{$model}->find('all', $search);
		if ($model == 'User') {
			$ids = Set::extract('/User/id', $results);
		} else {
			$ids = Set::extract('/'.$model.'/user_id', $results);
		}

		// only need name, picture and address
		$results = $this->User->find('all', array(
			'conditions' => array(
				'User.id' => $ids
			),
			'contain' => array(
				'Address',
				'ActiveAddress',
				'Profile' => array(
					'fields' => array('name')
				),
				'Image'
			)
		));

		// fill in missing geocoordinates - while this logic belongs in the model
		// layer afterFind isn't called on contained associations
		foreach ($results as $key => $result) {
			// has many
			foreach ($result['Address'] as $addressKey => $address) {
				if (!empty($address['id']) && ($address['lat'] == 0.0000000 || $address['lng'] == 0.0000000)) {
					$coords = $this->User->Address->geoCoordinates($address);
					if (isset($coords['lat']) && isset($coords['lng'])) {
						$this->User->Address->id = $address['id'];
						$this->User->Address->saveField('lat', $coords['lat']);
						$this->User->Address->saveField('lng', $coords['lng']);
						$address['lat'] = $coords['lat'];
						$address['lng'] = $coords['lng'];
					}
				}
				$results[$key]['Address'][$addressKey] = $address;
			}

			// has one
			$address = $result['ActiveAddress'];
			if (!empty($address['id']) && ($address['lat'] == 0.0000000 || $address['lng'] == 0.0000000)) {
				$coords = $this->User->Address->geoCoordinates($address);
				if (isset($coords['lat']) && isset($coords['lng'])) {
					$this->User->Address->id = $address['id'];
					$this->User->Address->saveField('lat', $coords['lat']);
					$this->User->Address->saveField('lng', $coords['lng']);
					$address['lat'] = $coords['lat'];
					$address['lng'] = $coords['lng'];
				}
			}
			$results[$key]['ActiveAddress'] = $address;
		}

		$this->set(compact('results', 'model'));
	}
}
