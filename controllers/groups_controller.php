<?php

class GroupsController extends AppController {

	var $name = 'Groups';
	
/**
 * Model::beforeFilter() callback
 *
 * Sets permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index');
	}
	
	function index() {
		$this->set('groups', $this->Group->generatetreelist(null, null, null, '&nbsp;&nbsp;'));
	}
	
}
?>