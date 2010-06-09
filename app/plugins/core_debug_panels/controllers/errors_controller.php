<?php

class ErrorsController extends AppController {
	
	var $name = 'Errors';
	
	var $helpers = array('Formatting');

/*
 * Shows a list of errors. Used by DebugKit
 *
 * @param string $levelFilter An quick additional filter
 */
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

		$this->set('content', $this->paginate());	
	}

}

?>