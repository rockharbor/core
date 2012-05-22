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
		$this->Auth->allow('calendar');
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
 * If any of the three searchable models (User, Ministry, or Involvement) are
 * passed, they are used as filters. The named parameter value can be a
 * comma-delimited list of ids.
 * 
 * For example:
 * {{{
 * /dates/calendar/User:1,2,3/Ministry:1/Involvement:5,6 
 * }}}
 * The above would pull all involvements for users 1, 2 and 3, include all 
 * involvements for ministry 1 and include the two involvements 5 and 6.
 * 
 * For BC purposes, the 'User' parameter acts differently than the other two in
 * that it forces the calendar to return *only* events that user is involved in,
 * while specifying 'Ministry' or 'Involvement' will include the specified ids
 * in addition to the original search.
 *
 * @param string $size A mini or full-size calendar
 * @todo Make all parameters act the same (may require special action for User calendar)
 */ 
	function calendar($size = 'mini') {
		$this->set(compact('size'));
		
		// if it's not the calendar calling, just leave. there's nothing 
		// special to pass to the calendar view
		if (!isset($this->params['url']['ext']) || $this->params['url']['ext'] != 'json') {
			return;
		}
				
		// check for filtering and add extra conditions
		$conditions = array();
		$link = array(
			'Date' => array(
				'fields' => array(
					'id'
				)
			)
		);
		$involvementIds = array();
		foreach (array('User', 'Ministry', 'Involvement', 'Campus') as $model) {
			if (isset($this->passedArgs[$model])) {
				$this->passedArgs[$model] = preg_replace('/[^\d\,]/', '', $this->passedArgs[$model]);
				$ids = explode(',', $this->passedArgs[$model]);
				switch ($model) {
					case 'User':
						// get involvements user is involved with first
						$rosters = $this->Date->Involvement->Roster->find('all', array(
							'conditions' => array(
								'Roster.user_id' => $ids
							)
						));
						$leaders = $this->Date->Involvement->Leader->find('all', array(
							'conditions' => array(
								'Leader.user_id' => $ids,
								'Leader.model' => 'Involvement'
							)
						));
						$rosterIds = Set::extract('/Roster/involvement_id', $rosters);
						$leaderIds = Set::extract('/Leader/model_id', $leaders);
						$involvementIds = array_merge($involvementIds, $rosterIds, $leaderIds);
						// if user has none, make sure no involvements show
						if (empty($involvementIds)) {
							$involvementIds = array(0);
						}
					break;
					case 'Involvement':
						$conditions['or']['Involvement.id'] = $ids;
					break;
					case 'Ministry':
						$conditions['or']['Involvement.ministry_id'] = $ids;
					break;
					case 'Campus':
						$conditions['or']['Ministry.campus_id'] = $ids;
						$link[] = 'Ministry';
					break;
				}			
			}
		}
		
		$options = array();
		if (isset($this->params['url']['start'])) {
			$options['start'] = $this->params['url']['start'];
		}
		if (isset($this->params['url']['end'])) {
			$options['end'] = $this->params['url']['end'];
		}
		
		if (!empty($involvementIds)) {
			$conditions['Involvement.id'] = array_unique($involvementIds);
		}
		$conditions['Involvement.active'] = true;
		$conditions['Involvement.private'] = false;
		$conditions['Date.start_date <>'] = null;
		
		// get all involvements and their dates within the range
		$involvements = $this->Date->Involvement->find('all', array(
			'fields' => array('id', 'name'),
			'link' => $link,
			'conditions' => $conditions,
			'group' => 'Involvement.id'
		));
		
		$events = array();
		foreach ($involvements as $involvement) {
			$involvement_dates = $this->Date->generateDates($involvement['Involvement']['id'], $options);

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
		$this->set('dates', $this->Date->find('all', array(
			'conditions' => array(
				'involvement_id' => $this->passedArgs['Involvement']
			)
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
				$this->Session->setFlash(__('This date has been created.', true), 'flash'.DS.'success');
			} else {
				$this->Session->setFlash(__('Unable to create date. Please try again.', true), 'flash'.DS.'failure');
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
			$this->cakeError('error404');
		}
		if (!empty($this->data)) {
			if ($this->Date->save($this->data)) {
				$this->Session->setFlash('This date has been updated.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Unable to update date. Please try again.', 'flash'.DS.'failure');
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
			$this->cakeError('error404');
		}
		if ($this->Date->delete($id)) {
			$this->Session->setFlash(__('This date has been deleted.', true), 'flash'.DS.'success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Unable to delete date. Please try again.', true), 'flash'.DS.'failure');
		$this->redirect(array('action' => 'index'));
	}
}
?>