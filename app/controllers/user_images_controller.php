<?php
/**
 * User Image controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Includes
 */
App::import('Controller', 'Images');

/**
 * UserImages Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class UserImagesController extends ImagesController {

/**
 * The name of the model to associate the Image with
 *
 * @var string
 */
	var $model = 'User';

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
		$this->modelId = isset($this->passedArgs[$this->model]) ? $this->passedArgs[$this->model] : null;
	}

/**
 * All un-approved images
 */
	function index() {
		$this->viewPath = 'user_images';
		$this->set('images', $this->Image->find('all', array(
			'conditions' => array(
				'group' => 'Image',
				'model' => 'User',
				'approved' => false
			)
		)));
	}
	
}