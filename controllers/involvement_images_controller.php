<?php
/**
 * Involvement Image controller class.
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
 * InvolvementImages Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class InvolvementImagesController extends ImagesController {

/**
 * The name of the model to associate the Image with
 *
 * @var string
 */
	public $model = 'Involvement';

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->modelId = isset($this->passedArgs[$this->model]) ? $this->passedArgs[$this->model] : null;
	}

}