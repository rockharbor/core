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
 * If true, an empty array will be returned if no pagination data is present.
 * This is useful for searches, since you don't want to display ALL results the
 * first time the user reaches the page. Set to false to display the paginated
 * data either way.
 *
 * @var boolean
 */
	var $startEmpty = true;

/**
 * Start FilterPaginationComponent for use in the controller
 *
 * @param object $controller A reference to the controller
 * @access public
 */
	function initialize(&$controller, $settings = array()) {
		$this->controller =& $controller;
		$this->_set($settings);
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
		if (isset($this->controller->{$model})) {
			$model = $this->controller->{$model};
		} elseif (isset($this->controller->{$this->controller->modelClass}->{$model})) {
			$model = $this->controller->{$this->controller->modelClass}->{$model};
		} else {
			$model = ClassRegistry::init($model);
		}
		
		// new search, remove saved filter
		if ($this->startEmpty) {
			if (!empty($this->controller->data) || !isset($this->controller->params['named']['page'])) {
				$this->Session->delete('FilterPagination');
			}
		} else {
			if (!empty($this->controller->data) && $this->controller->data != $this->Session->read('FilterPagination.data')) {
				$this->Session->delete('FilterPagination');
			}
		}	
		
		if (!$this->Session->check('FilterPagination')) {
			// save data in session if it's not there
			$this->Session->write('FilterPagination.paginate', $this->controller->paginate);
			$this->Session->write('FilterPagination.data', $this->controller->data);
			// conserve any after-the-fact model bindings
			$this->Session->write('FilterPagination.'.$model->alias.'.hasOne', $model->hasOne);
			$this->Session->write('FilterPagination.'.$model->alias.'.belongsTo', $model->belongsTo);
		} elseif (isset($this->controller->params['named']['page']) || !$this->startEmpty) {
			// otherwise use it for pagination and data
			$this->controller->paginate = $this->Session->read('FilterPagination.paginate');
			$this->controller->data = $this->Session->read('FilterPagination.data');
			$model->hasOne = $this->Session->read('FilterPagination.'.$model->alias.'.hasOne');
			$model->belongsTo = $this->Session->read('FilterPagination.'.$model->alias.'.belongsTo');
		}
		
		// return empty array by default so we don't perform a search without filtering first
		if (!empty($this->controller->data) || isset($this->controller->params['named']['page']) || !$this->startEmpty) {
			return $this->controller->paginate($model);
		} else {
			return array();
		}
	}
	
	
}