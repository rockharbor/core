<?php
/**
 * Document controller class.
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
 * Documents Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class DocumentsController extends AttachmentsController {

/**
 * The name of the controller
 *
 * @var string
 */
	public $name = 'Documents';

/**
 * The name of the model this Address belongs to. Used for Acl
 *
 * @var string
 */
	public $model = null;

/**
 * The id of the model this Address belongs to. Used for Acl
 *
 * @var integer
 */
	public $modelId = null;

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 */
	public function beforeFilter() {
		parent::beforeFilter();
	}

}
