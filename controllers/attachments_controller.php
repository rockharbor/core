<?php
/**
 * Attachment controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Attachments Controller
 *
 * This controller is not accessed directly, but rather extended by different controllers
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class AttachmentsController extends AppController {

/**
 * Force the child (documents/images) to use this view path
 *
 * @var string viewPath
 */
	var $viewPath = 'attachments';

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array(
		'Media.Media',
		'Number',
		'Formatting'
	);
	
/**
 * The name of the model this Attachment belongs to. Used for Acl
 *
 * @var string
 */
	var $model = null;

/**
 * The id of the model this Attachment belongs to. Used for Acl
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
		$this->modelClass = Inflector::singularize($this->name);
		$this->{$this->modelClass}->model = $this->model;
	}

/**
 * Model::beforeRender() callback.
 *
 * @access private
 */
	function beforeRender() {
		parent::beforeRender();

		$this->set('attachmentModel', $this->modelClass);
		$this->set('model', $this->model);
		$this->set('modelId', $this->modelId);
	}	

/**
 * Shows a list of attachments, along with an upload form.
 */ 
	function index() {
		$this->{$this->modelClass}->recursive = 0;
		
		$this->set('attachments', $this->{$this->modelClass}->find('all', array(
			'conditions' => array(
				'foreign_key' => $this->modelId,
				'group' => $this->modelClass,
				'model' => $this->model
			)
		)));
	}

/**
 * Downloads an attachment
 *
 * @param integer $id The id of the attachment
 */ 
	function download($id) {
		$this->view = 'Media';
		
		$this->{$this->modelClass}->recursive = -1;
		$attachment = $this->{$this->modelClass}->read(null, $id);
		
		$ext = array_pop(explode('.', $attachment[$this->modelClass]['basename']));
		
		$params = array(
			'id' => $attachment[$this->modelClass]['basename'],
			'name' => $attachment[$this->modelClass]['alternative'],
			'download' => true,
			'extension' => $ext,
			'mimeType' => array($ext => Mime_Type::guessType(MEDIA.$attachment[$this->modelClass]['dirname'].DS.$attachment[$this->modelClass]['basename'])),
			'path' => MEDIA_TRANSFER.$attachment[$this->modelClass]['dirname'].DS
		);

		$this->set($params);
	}

/**
 * Uploads an attachment
 *
 * To be sent via a file upload form. The named parameters 'model' and $model
 * should be sent so the file can be attached to the user, involvement, or whatever.
 */ 	
	function upload() {
		$settingName = Inflector::pluralize(strtolower($this->model)).'.'.strtolower($this->model).'_'.strtolower($this->modelClass).'_limit';
		$attachments = $this->{$this->modelClass}->find('all', array(
			'conditions' => array(
				'foreign_key' => $this->modelId,
				'group' => $this->modelClass,
				'model' => $this->model
			)
		));
		if (!empty($this->data) && (count($attachments) < (Core::read($settingName) !== null ? Core::read($settingName) : 1))) {
			$friendly = explode('.', $this->data[$this->modelClass]['file']['name']);
			array_pop($friendly);
			$friendly = implode('.', $friendly);
			$this->data[$this->modelClass]['alternative'] = low($friendly);
			$this->data[$this->modelClass]['approved'] = false;
			if ($this->isAuthorized('attachments/approve')) {
				$this->data[$this->modelClass]['approved'] = true;
			}

			if ($this->{$this->modelClass}->save($this->data)) {
				$this->Session->setFlash(Inflector::humanize($this->modelKey).' added!', 'flash'.DS.'success');
			}
			//failures will be handled by error messages, no need to set flash since they'll see an error (no ajax)
			//and ajax uploads handle the errors nicely
		}

		$this->set(compact('attachments'));
	}

/**
 * Approves or denies an attachment. Attachments that are denied are deleted
 *
 * @param integer $id The attachment id
 * @param boolean $approve Whether or not to approve
 */
	function approve($id = null, $approve = false) {
		if (!$id) {
			$this->Session->setFlash('Invalid id', 'flash'.DS.'failure');
		} else {
			if ($approve) {
				if ($this->{$this->modelClass}->saveField('approved', true)) {
					$this->Session->setFlash(Inflector::humanize($this->modelKey).' approved', 'flash'.DS.'success');
				} else {
					$this->Session->setFlash(Inflector::humanize($this->modelKey).' could not be approved', 'flash'.DS.'failure');
				}
			} else {
				$this->setAction('delete', $id);
			}
		}

		$this->redirect(array(
			'action' => 'index',
			$this->model => $this->modelId
		));
	}
	
/**
 * Deletes an attachment and removes the file.
 *
 * @param integer $id The id of the attachment
 */ 
	function delete($id = null) {		
		if (!$id) {
			$this->Session->setFlash('Invalid id', 'flash'.DS.'failure');
		} else {		
			if ($this->{$this->modelClass}->delete($id)) {
				$this->Session->setFlash(Inflector::humanize($this->modelKey).' deleted', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash(Inflector::humanize($this->modelKey).' was not deleted', 'flash'.DS.'failure');
			}	
		}
		
		$this->redirect(array(
			'action' => 'index',
			$this->model => $this->modelId
		));	
	}
}

?>