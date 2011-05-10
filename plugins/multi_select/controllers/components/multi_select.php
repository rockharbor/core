<?php
/**
 * Multi select component class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       multi_select
 * @subpackage    multi_select.controllers.components
 */

/**
 * MultiSelect Component
 *
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
 * @package       multi_select
 * @subpackage    multi_select.controllers.components
 */
class MultiSelectComponent extends Object {

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
 * Current token
 *
 * @var string
 * @access protected
 */
	var $_token = null;

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
 * Creates session keys and removes expired ones
 *
 * @access public
 * @todo check for pagination in url instead of layout
 */	
	function startup() {
		if (!isset($this->controller->params['named'])) {
			$this->controller->params['named'] = array();
		}
		
		if ($this->check()) {
			$currentTokens = $this->Session->read('MultiSelect');
			foreach ($currentTokens as $token => $values) {
				if (isset($values['created']) && $values['created'] > strtotime('+10 minutes')) {
					$this->Session->delete('MultiSelect.'.$token);
				}
			}
		}

		if (!isset($this->controller->passedArgs['mstoken'])) {
			$this->_token = String::uuid();
			$this->controller->params['named']['mstoken'] = $this->_token;
			$this->controller->passedArgs['mstoken'] = $this->_token;
			$success = $this->Session->write('MultiSelect.'.$this->_token, array(
				'selected' => array(),
				'search' => array(),
				'page' => array(),
				'created' => time()
			));
		} else {
			$this->_token = $this->controller->passedArgs['mstoken'];
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
		if (!is_array($search)) {
			return false;
		}

		unset($search['limit']);
		return $this->Session->write('MultiSelect.'.$this->_token.'.search', $search);
	}

/**
 * Retrieves search data
 *
 * @param string $uid The token to get, or null for current
 * @return array Search data that was previously saved
 * @access public
 */	
	function getSearch($uid = null) {
		if (!$uid) {
			$uid = $this->_token;
		}
		return $this->Session->read('MultiSelect.'.$uid.'.search');
	}

/**
 * Retrieves selected ids
 *
 * @param string $uid The token to get, or null for current
 * @return mixed Array of ids, or `all`
 * @access public
 */		
	function getSelected($uid = null) {
		if (!$uid) {
			$uid = $this->_token;
		}
		return $this->Session->read('MultiSelect.'.$uid.'.selected');
	}

/**
 * Checks to see if MultiSelect session is set, or if an id is a MultiSelect id
 *
 * @param $uid The id to check, if any
 * @return boolean Exists?
 * @access public
 */		
	function check($uid = null) {
		if (!$uid) {
			return $this->Session->check('MultiSelect');
		} else {
			return $this->Session->check('MultiSelect.'.$uid);
		}
	}

/**
 * Merges ids with current selected ids
 *
 * @param array $data Array of ids
 * @return array New data
 * @access public
 */		
	function merge($data = array()) {
		$cache = array_values(array_unique(array_merge($this->_get(), $data)));
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
 * Saves current page into selected
 *
 * @return boolean Set success
 * @access public
 */			
	function selectAll() {
		return $this->merge($this->Session->read('MultiSelect.'.$this->_token.'.page'));
	}

/**
 * Removes current page from selected
 *
 * @return boolean Reset success
 * @access public
 */		
	function deselectAll() {
		return $this->delete($this->Session->read('MultiSelect.'.$this->_token.'.page'));
	}

/**
 * Saves ids in the session key
 *
 * @param array $data Array of ids
 * @return boolean Write success
 * @access private
 */	
	function _save($data = array()) {
		return $this->Session->write('MultiSelect.'.$this->_token.'.selected', $data);
	}

/**
 * Gets ids from the session key
 *
 * @return array Array of ids
 * @access private
 */		
	function _get($uid = null) {
		if (!$uid) {
			$uid = $this->_token;
		}
		return $this->Session->read('MultiSelect.'.$this->_token.'.selected');
	}	
}
?>