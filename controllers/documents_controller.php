<?php

App::import('Controller', 'Attachments');

class DocumentsController extends AttachmentsController {

	var $name = 'Documents';
	
	var $model = null;
	var $modelId = null;
	
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

}

?>