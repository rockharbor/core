<?php

App::import('Controller', 'Attachments');

class ImagesController extends AttachmentsController {
	
	var $name = 'Images';
	
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
	
/**
 * Displays an image
 *
 * Defaults to the first image for the model if no 
 * id is specified.
 */ 	
	function view($id = 0, $size = 's') {	
		// use this view path now, instead of attachments
		$this->viewPath = 'images';
		
		// get image
		$image = array();
		$this->Image->recursive = -1;
		if ($id == 0) {
			$image = $this->Image->find('first', array(
				'conditions' => array(
					'model' => $this->model,					
					'foreign_key' => $this->modelId,
					'group' => 'Image'
				)
			));
		} else {
			$image = $this->Image->read(null, $id);
		}
		
		$this->set('image', $image);
		$this->set('size', $size);
	}

}

?>