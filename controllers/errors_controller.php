<?php

class ErrorsController extends AppController {
	
	var $name = 'Errors';
	
	var $helpers = array('Formatting');

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
	
	function index($levelFilter = null) {
		$this->Error->recursive = 0;
		$this->Error->order = 'Error.created DESC';
		
		if ($levelFilter) {
			$this->paginate = array(
				'conditions' => array(
					'level' => $levelFilter
				)
			);
		}
		
		$this->set('logs', $this->paginate());
	}

}

?>