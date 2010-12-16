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
	var $name = 'Reports';

/**
 * List of models this controller uses
 *
 * @var string
 */
	var $uses = array('User', 'Roster', 'Ministry', 'Involvement', 'Campus');

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array('GoogleMap', 'Media.Media', 'Report');

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
	}

/**
 * Reports home page
 */
	function index() {	
	}

/**
 * Display statistics about ministries and their involvement opportunities
 */ 
	function ministry() {
		$contain = array(
			'Involvement' => array(
				'Leader'
			),
			'Leader'
		);
		
		if (!empty($this->data)) {
			$conditions = $this->postConditions($this->data);
			$conditions = Set::filter($conditions);
			$ministries = $this->Ministry->find('all', array(
				'conditions' => $conditions,
				'contain' => $contain
			));
		} else {
			$ministries = $this->Ministry->find('all', array(
				'contain' => $contain
			));
		}
		
		$campuses = $this->Campus->find('list');
		$ministryList = $this->Ministry->generatetreelist();
		$involvementTypes = $this->Involvement->InvolvementType->find('list');
		
		$this->set(compact('ministries', 'campuses', 'ministryList', 'involvementTypes'));
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
	function export($model, $uid) {
		if (!empty($this->data)) {
			$options = array();
			if ($this->data['Export']['type'] == 'csv') {
				$this->set('title_for_layout', strtolower($model).'-search-export');
				$options['attachment'] = $this->viewVars['title_for_layout'].'.csv';
			}
			// set render path (which sets response type)
			$this->RequestHandler->renderAs($this, $this->data['Export']['type'], $options);
			unset($this->data['Export']['type']);
			
			$search = $this->MultiSelect->getSearch($uid);
			$selected = $this->MultiSelect->getSelected($uid);
			// assume they want all if they didn't select any
			if (!empty($selected)) {
				$search['conditions'][$model.'.id'] = $selected;
			}
			
			$results = $this->{$model}->find('all', $search);
			
			$this->set('models', $this->data['Export']);
			$this->set('results', $results);
		}
		
		$this->set(compact('uid', 'model'));
	}

/**
 * Shows a map from a list of results
 *
 * @param string $model The name of the model to search
 * @param string $uid The MultiSelect cache key to get results from
 */
	function map($model, $uid) {
		$search = $this->MultiSelect->getSearch($uid);
		$selected = $this->MultiSelect->getSelected($uid);
		// assume they want all if they didn't select any
		if (!empty($selected)) {
			$search['conditions'][$model.'.id'] = $selected;
		}
		
		// only need name, picture and address
		$search['contain'] = array();
		$contain = array(
			'Profile',
			'Image',
			'Address' => array(
				'conditions' => array(
					'Address.primary' => true
				)
			)
		);
		if ($model !== 'User') {
			$search['contain']['User'] = $contain;
			$results = $this->User->{$model}->find('all', $search);
			$this->set('results', Set::extract('/User/.', $results));
		} else {
			$search['contain'] = $contain;
			$results = $this->User->find('all', $search);
			$this->set('results', $results);
		}		
	}
}
?>