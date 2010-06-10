<?php

class ErrorsController extends CoreDebugPanelsAppController {
	
	var $name = 'Errors';
	
	var $helpers = array('Formatting');

/*
 * Shows a filtered list of errors
 *
 * @param string $levelFilter An quick additional filter
 * @param integer $limit Pagination limit
 */
	function filter($levelFilter = 'all', $limit = 10) {
		$this->Error->recursive = 0;
		$this->Error->order = 'Error.created DESC';
		
		if ($levelFilter != 'all') {
			$conditions = array(
				'Error.level' => $levelFilter
			);
		}

		$this->set('content', $this->Error->find('all', compact('conditions', 'limit')));
	}

}

?>