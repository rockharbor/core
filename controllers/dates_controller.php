<?php
/**
 * Date controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Dates Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class DatesController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Dates';

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array('SelectOptions', 'Formatting');

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
 * Model::beforeRender() callback
 */ 
	function beforeRender() {
		parent::beforeRender();
		
		$this->set('recurranceTypes', $this->Date->recurranceTypes);
	}

/**
 * Displays a calendar
 *
 * ### Params:
 *
 *	- `start` The start timestamp
 *	- `end` The end timestamp
 *
 * ### Passed Args:
 *
 * - `model` A model to filter by
 * - `model.id` The model's id to filter by
 *
 * @param string $size A mini or full-size calendar
 */ 
	function calendar($size = 'mini') {
		$filters = array();
		
		if (isset($this->passedArgs['model'])) {
			$filterModel = $this->passedArgs['model'];
			
			if (isset($this->passedArgs[$this->passedArgs['model']])) {
				$filterModelId = $this->passedArgs[$this->passedArgs['model']];
				$filters['model'] = $filterModel;
				$filters[$filterModel] = $filterModelId;
			}
		}
		
		$this->set(compact('filters', 'size'));
		
		// if it's not the calendar calling, just leave. there's nothing 
		// special to pass to the calendar view		
		if (!isset($this->params['url']['ext']) || $this->params['url']['ext'] != 'json') {
			return;
		}
				
		// check for filtering and add extra conditions
		$filter = array();
		$link = array();
		if (isset($this->passedArgs['model']) && isset($this->passedArgs[$this->passedArgs['model']])) {
			switch ($this->passedArgs['model']) {
				case 'User':
					$filter = array(
						'or' => array(
							'Roster.user_id' => $this->passedArgs[$this->passedArgs['model']],
							'Leader.user_id' => $this->passedArgs[$this->passedArgs['model']]
						)
					);
					$link = array(
						'Roster' => array(),
						'Leader' => array()
					);
				break;
				case 'Involvement':
					$filter = array(
						'Involvement.id' => $this->passedArgs[$this->passedArgs['model']]
					);
				break;
			}			
		}
		
		$range = array(
			'start' => date('Y-m-d H:i', $this->params['url']['start']),
			'end' => date('Y-m-d H:i', $this->params['url']['end']) 
		);
		
		// currently we're grabbing this event. we want to grab all public and published
		// events, then pair them with their dates
		$events = array();
		
		$conditions = $filter;
		$conditions['Involvement.has_dates'] = true;
		
		// get all involvements and their dates within the range
		$involvements = $this->Date->Involvement->find('all', array(
			'fields' => array('id', 'name'),
			'link' => $link,
			'conditions' => $conditions
		));

		foreach ($involvements as $involvement) {
			$involvement_dates = $this->Date->generateDates($involvement['Involvement']['id'], $range);

			if (!empty($involvement_dates)) {
				$events[] = array_merge($involvement, array('dates' => $involvement_dates));
			}
		}
		$this->set(compact('events'));
	}

/**
 * Displays a list of dates
 */ 
	function index() {		
		$this->Date->recursive = 0;
		$this->set('dates', $this->paginate(array(
			'involvement_id' => $this->passedArgs['Involvement']
		)));
		$this->set('involvementId', $this->passedArgs['Involvement']);
	}

/**
 * Adds a date
 */ 
	function add() {
		if (!empty($this->data)) {		
			$this->Date->create();
			if ($this->Date->save($this->data)) {
				$this->Session->setFlash(__('The date has been saved', true));
			} else {
				$this->Session->setFlash(__('The date could not be saved. Please, try again.', true));
			}
		}
		
		$this->set('involvementId', $this->passedArgs['Involvement']);
	}

/**
 * Edits a date
 *
 * @param integer $id The id of the date to edit
 */ 
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid date', true));
		}
		if (!empty($this->data)) {
			if ($this->Date->save($this->data)) {
				$this->Session->setFlash('The date has been saved', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('The date could not be saved. Please, try again.', 'flash'.DS.'failure');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Date->read(null, $id);
		}
	}

/**
 * Deletes a date
 *
 * @param integer $id The id of the date to delete
 */ 
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for date', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Date->delete($id)) {
			$this->Session->setFlash(__('Date deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Date was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>