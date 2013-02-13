<?php
/**
 * AppSetting Image controller class.
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
 * AppSettingImages Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class AppSettingImagesController extends ImagesController {

/**
 * The name of the model to associate the Image with
 *
 * @var string
 */
	var $model = 'AppSetting';

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

}