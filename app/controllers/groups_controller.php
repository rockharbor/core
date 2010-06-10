<?php
/**
 * Group controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Groups Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class GroupsController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Groups';
	
/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index');
	}

/**
 * Shows a tree'd list of the current groups
 */
	function index() {
		$this->set('groups', $this->Group->generatetreelist(null, null, null, '&nbsp;&nbsp;'));
	}
	
}
?>