<?php
/**
 * Image controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Includes
 */
App::import('Controller', 'Attachments');

/**
 * Images Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class ImagesController extends AttachmentsController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Images';

/**
 * The name of the model this Address belongs to. Used for Acl
 *
 * @var string
 */
	var $model = null;

/**
 * The id of the model this Address belongs to. Used for Acl
 *
 * @var integer
 */
	var $modelId = null;

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */
	function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * Displays an image
 *
 * Defaults to the first image for the model if no id is specified.
 *
 * @param integer $id The id of the image to view
 * @param string $size The size of the filtered image to display
 */
	function view($id = 0, $size = 's') {
		// use this view path now, instead of attachments
		$this->viewPath = 'images';

		// get image
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

		$this->set(compact('image', 'size'));
	}

/**
 * All un-approved images
 */
	function approval() {
		$this->viewPath = 'images';
		$this->set('images', $this->paginate(array(
				'group' => 'Image',
				'approved' => false
		)));
	}

}
