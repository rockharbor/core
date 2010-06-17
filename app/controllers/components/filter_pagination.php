<?php
/**
 * Filter pagination component class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers.components
 */

/**
 * FilterPagination Component
 *
 * Use Controller::FilterPagination::paginate() instead of Controller::paginate in order to save the pagination
 * data to be used later. For example, if you search and wish to paginate the results, use FilterPagination's paginate
 * instead of the built in paginate function. Everything else (helper, etc.) remains the same and it should be
 * fairly transparent.
 *
 * *NOTE:* By default, if there is no data and this is not a pagination request, FilterPagination::paginate() returns an
 * empty array instead of Controller::paginate()'s default resultset.
 * 
 * @package       core
 * @subpackage    core.app.controllers.components
 */
class FilterPaginationComponent extends Object {

/**
 * A stored reference to the calling controller
 *
 * @var object
 * @access public
 */ 
	var $controller = null;

/**
 * Components the FilterPaginationComponent uses
 *
 * @var array
 * @access public
 */ 
	var $components = array('Session');

/**
 * Start FilterPaginationComponent for use in the controller
 *
 * @param object $controller A reference to the controller
 * @access public
 */
	function initialize(&$controller) {
		$this->controller =& $controller;
	}
	
/**
 * Saves search parameters in the Session then paginates 
 * using CakePHP's paginate function
 *
 * @param string $model Model to paginate
 * @return array Cake results
 * @access public
 * @see Controller::paginate()
 */	
	function paginate($model = null) {
		if (!$model) {
			$model = $this->controller->modelClass;
		}
		
		// new search, remove saved filter
		if (!empty($this->controller->data) || !isset($this->controller->params['named']['page'])) {
			$this->Session->delete('FilterPagination');
		}
		
		if (!$this->Session->check('FilterPagination')) {
			// save data in session if it's not there
			$this->Session->write('FilterPagination.paginate', $this->controller->paginate);
			$this->Session->write('FilterPagination.data', $this->controller->data);
			// conserve any after-the-fact model bindings
			$this->Session->write('FilterPagination.'.$model.'.hasOne', $this->controller->{$model}->hasOne);
			$this->Session->write('FilterPagination.'.$model.'.belongsTo', $this->controller->{$model}->belongsTo);
		} elseif (isset($this->controller->params['named']['page'])) {
			// otherwise use it for pagination and data
			$this->controller->paginate = $this->Session->read('FilterPagination.paginate');
			$this->controller->data = $this->Session->read('FilterPagination.data');
			$this->controller->{$model}->hasOne = $this->Session->read('FilterPagination.'.$model.'.hasOne');
			$this->controller->{$model}->belongsTo = $this->Session->read('FilterPagination.'.$model.'.belongsTo');
		}
		
		// return empty array by default so we don't perform a search without filtering first
		if (!empty($this->controller->data) || isset($this->controller->params['named']['page'])) {
			return $this->controller->paginate($model);
		} else {
			return array();
		}
	}
	
	
}