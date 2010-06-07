<?php

/**
 * Allows for storing multiple ids of a search to return later.
 *
 * In your controller, use MultiSelectComponent::saveSearch() to save your search. Pass the conditions,
 * contain - anything you would use in a Model::find() or Controller::paginate(). Use the MultiSelectHelper
 * to build the checkboxes. For this to work, you'll also need Controller::multi_select_session() to be defined
 * and authorized by the current controller.
 *
 * When an action passes the key to your new controller action, use MultiSelectComponent::getSearch() to get
 * your saved search data, then use MultiSelectComponent::getSelected() and modify the search parameters 
 * appropriately. MultiSelectComponent::getSelected() returns `all` if the check all checkbox was selected.
 *
 * @author 		Jeremy Harris <jharris@rockharbor.org>
 * @package		app
 * @subpackage	app.controllers.components
 */
class MultiSelectComponent extends Object {

/**
 * The current/active cache key
 *
 * @var string
 * @access public
 */ 
	var $cacheKey = null;
/**
 * A stored reference to the calling controller
 *
 * @var object
 * @access public
 */ 
	var $controller = null;

/**
 * Components the MultiSelectComponent uses
 *
 * @var array
 * @access public
 */ 
	var $components = array('Session');

/**
 * Start MultiSelectComponent for use in the controller
 *
 * @param object $controller A reference to the controller
 * @access public
 */
	function initialize(&$controller) {
		$this->controller =& $controller;		
	}

/**
 * Creates session keys
 *
 * @access public
 */	
	function startup() {
		// Creates MultiSelectHelper keys only if it's a new 'page'
		if (!$this->controller->RequestHandler->isAjax() && $this->controller->layout == 'default') {
			$cacheKey = uniqid();
			$this->Session->write('MultiSelect.cacheKey', $cacheKey);
			$this->Session->write('MultiSelect.'.$cacheKey.'.selected', array());
			$this->Session->write('MultiSelect.'.$cacheKey.'.search', array());
			$this->Session->write('MultiSelect.'.$cacheKey.'.all', false);
			
			$this->cacheKey = $cacheKey;
		} else {
			$this->cacheKey = $this->Session->read('MultiSelect.cacheKey');
		}
	}

/**
 * Stores search data for later use
 *
 * @param array $search The search data
 * @return boolean Save success
 * @access public
 */	
	function saveSearch($search = array()) {
		unset($search['limit']);
		return $this->Session->write('MultiSelect.'.$this->cacheKey.'.search', $search);
	}

/**
 * Retrieves search data
 *
 * @param array $key The MultiSelect key to get from. By default, it uses the current key
 * @return array Search data that was previously saved
 * @access public
 */	
	function getSearch($key = null) {
		if (!$key) {
			$key = $this->cacheKey;
		}
		
		return $this->Session->read('MultiSelect.'.$key.'.search');
	}

/**
 * Retrieves selected ids
 *
 * Returns `all` if the check all box was selected. Modify your search appropriately.
 *
 * @param array $key The MultiSelect key to get from.
 * @return mixed Array of ids, or `all`
 * @access public
 */		
	function getSelected($key = null) {
		return ($this->Session->read('MultiSelect.'.$this->cacheKey.'.all')) ? 'all' : $this->_get($key);
	}

/**
 * Checks to see if an id is a MultiSelect id (for controller functions that use $id as a single or multiple)
 *
 * @param array $id The id
 * @return boolean Exists?
 * @access public
 */		
	function check($id = null) {
		return $this->Session->check('MultiSelect.'.$id);
	}

/**
 * Merges ids with current selected ids
 *
 * @param array $data Array of ids
 * @return array New data
 * @access public
 */		
	function merge($data = array()) {
		$cache = array_unique(array_merge($this->_get(), $data));
		$this->_save($cache);
		return $cache;
	}

/**
 * Appends ids to current selected ids
 *
 * @param array $data Array of ids
 * @return array New data
 * @access public
 */			
	function append($data = array()) {
		$cache = array_merge($this->_get(), $data);
		$this->_save($cache);
		return $cache;
	}

/**
 * Removes ids from current selected ids
 *
 * @param array $data Array of ids
 * @return array New data
 * @access public
 */			
	function delete($data = array()) {		
		$cache = array_values(array_diff($this->_get(), $data));
		$this->_save($cache);
		return $cache;
	}

/**
 * Sets `all` key and removes previous selections
 *
 * @return boolean Set success
 * @access public
 */			
	function selectAll() {
		// clear existing
		$this->Session->write('MultiSelect.'.$this->cacheKey.'.selected', array());
		return $this->Session->write('MultiSelect.'.$this->cacheKey.'.all', true);
	}

/**
 * Resets `all` key
 *
 * @return boolean Reset success
 * @access public
 */		
	function deselectAll() {
		return $this->Session->write('MultiSelect.'.$this->cacheKey.'.all', false);
	}

/**
 * Saves ids in the session key
 *
 * @param array $data Array of ids
 * @return boolean Write success
 * @access private
 */	
	function _save($data = array()) {
		return $this->Session->write('MultiSelect.'.$this->cacheKey.'.selected', $data);
	}

/**
 * Gets ids from the session key
 *
 * @param string $key The key to read. By default it uses the current one
 * @return array Array of ids
 * @access private
 */		
	function _get($key = null) {
		if (!$key) {
			$key = $this->cacheKey;
		}
		
		return $this->Session->read('MultiSelect.'.$key.'.selected');
	}
	
	
}
?>