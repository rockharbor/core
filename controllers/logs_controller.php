<?php
class LogsController extends AppController {

	var $name = 'Logs';

/**
 * Model::beforeFilter() callback
 *
 * Sets permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
	}
	
	function index() {
		$this->Log->recursive = 0;
		$this->set('logs', $this->paginate());
	}
}
?>