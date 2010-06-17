<?php
/**
 * Multi select helper class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.views.helpers
 */

/**
 * MultiSelect Helper
 *
 * Allows creation of multi select lists and actions
 *
 * Creates checkboxes and works with MultiSelectComponent and the PaginatorHelper to allow for
 * persisted selections across Ajax paginated pages. Pass MultiSelectComponent::cache to controllers
 * and read with MultiSelectComponent.
 *
 * @package       core
 * @subpackage    core.app.views.helpers
 */
class MultiSelectHelper extends AppHelper {

/**
 * Additional helpers
 *
 * @var array
 */ 
	var $helpers = array('Session', 'Form', 'Js');

/**
 * Ids that should be selected (set automatically by MultiSelectComponent)
 *
 * @var array
 */ 	
	var $selected = array();

/**
 * Current cache id
 *
 * @var string
 */ 
	var $cache = null;

/**
 * Initializes the Helper
 *
 * @access public
 */ 
	function create() {
		/*$View =& ClassRegistry::getObject('view');		
		// check for paginator in view		
		if (!isset($View->Paginator)) {
			trigger_error('MultiSelectHelper::create() :: Missing PaginatorHelper in view.', E_USER_WARNING);
		}*/
		// check for session key
		if (!$this->Session->check('MultiSelect.cacheKey')) {
			trigger_error('MultiSelectHelper::create() :: Missing MultiSelect.cacheKey key in session.', E_USER_WARNING);
		}
	
		// uid for current cache
		$this->cache = $this->Session->read('MultiSelect.cacheKey');
		
		// get cache and store
		$this->selected = $this->Session->read('MultiSelect.'.$this->cache.'.selected');
	}

/**
 * Creates a checkbox
 *
 * @param mixed $value The id to save, or `all` for a checkbox that selects all
 * @param array $options Array of options to merge with the checkbox
 * @return string The generated checkbox widget
 * @access public
 */ 
	function checkbox($value, $options = array()) {		
		$uid = String::uuid();
		
		$defaultOptions = array(
			'hiddenField' => false,
			'value' => $value,
			'class' => 'multi-select-box',
			'checked' => in_array($value, $this->selected) || $this->Session->read('MultiSelect.'.$this->cache.'.all')
		);
		
		$options = array_merge($defaultOptions, $options);
		$options['id'] = $uid;
		
		$output = $this->Form->checkbox('', $options);
		
		return $output;
	}
	
/**
 * Buffers JavaScript to tie it all together
 *
 * @access public
 */
	function end() {
		$this->Js->buffer('$(".multi-select-box[value=all]").bind("click", function() {
			selected = new Array();
			
			$(".multi-select-box").attr("checked", this.checked);
			
			url = "'.Router::url(array('action' => 'multi_select_session')).'";
			
			if (this.checked) {
				url += "/selectAll";
			} else {
				url += "/deselectAll";
			}
			
			url += ".json";
			
			$.ajax({url:url});
			
		});
		
		$(".multi-select-box[value!=all]").bind("click", function() {
			url = "'.Router::url(array('action' => 'multi_select_session')).'";
			
			if (this.checked) {
				url += "/merge";
			} else {
				url += "/delete";
			}
			
			url += "/"+$(this).val();
			
			url += ".json";
			
			$.ajax({url:url});
		});');
	}
}

?>