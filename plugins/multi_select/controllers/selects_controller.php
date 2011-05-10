<?php
/**
 * Selects controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       multi_select
 * @subpackage    multi_select.controllers
 */

/**
 * Selects Controller
 *
 * @package       multi_select
 * @subpackage    multi_select.controllers
 * @todo Parse json extension if not parsed already, then remove it after action
 */
class SelectsController extends AppController {

/**
 * Disable models for this controller
 *
 * @var array
 */
	var $uses = array();

/**
 * Components used by this controller
 * 
 * @var array
 */
	var $components = array(
		'RequestHandler',
		'MultiSelect.MultiSelect'
	);

/**
 * Allows simple session storage and manipulation for MultiSelectHelper and MultiSelectComponent
 *
 * ### Actions
 * - 'merge' Merge id with selected ids (no duplicates)
 * - 'delete' Removes id from selected ids
 * - 'selectAll' Include current page in list of selected ids
 * - 'deselectAll' Remove current page from selected
 *
 * @param string $action Action to take
 * @param string $data Comma delimited list of data
 * @access public
 */
	function session($action = 'deselectAll', $data = '') {
		// no access from anything other than the helper's functions
		if (!$this->RequestHandler->isAjax() || $this->RequestHandler->ext != 'json') {
			$this->cakeError('error404');
		}

		// call MultiSelect::$action
		$cache = $this->MultiSelect->{$action}(explode(',', $data));

		$this->set('data', $cache);
	}

}

?>