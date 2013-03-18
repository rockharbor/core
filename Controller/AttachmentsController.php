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
	public $viewPath = 'attachments';

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	public $helpers = array(
		'Media.Media',
		'Number',
		'Formatting'
	);

/**
 * Extra components for this controller
 *
 * @var array
 */
	public $components = array(
		'MultiSelect.MultiSelect'
	);

/**
 * The name of the model this Attachment belongs to. Used for Acl
 *
 * @var string
 */
	public $model = null;

/**
 * The id of the model this Attachment belongs to. Used for Acl
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
		$this->modelClass = Inflector::singularize($this->name);
		$this->{$this->modelClass}->model = $this->model;
	}

/**
 * Model::beforeRender() callback.
 */
	public function beforeRender() {
		parent::beforeRender();

		$this->set('attachmentModel', $this->modelClass);
		$this->set('model', $this->model);
		$this->set('modelId', $this->modelId);
	}

/**
 * Shows a list of attachments, along with an upload form.
 */
	public function index() {
		$this->{$this->modelClass}->recursive = 0;

		$this->set('attachments', $this->{$this->modelClass}->find('all', array(
			'conditions' => array(
				'foreign_key' => $this->modelId,
				'group' => $this->modelClass,
				'model' => $this->model
			)
		)));
		$this->set('limit', $this->_getLimit());
	}

/**
 * Downloads an attachment
 *
 * @param integer $id The id of the attachment
 */
	public function download($id) {
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
	public function upload() {
		$limit = $this->_getLimit();
		$attachments = $this->{$this->modelClass}->find('all', array(
			'conditions' => array(
				'foreign_key' => $this->modelId,
				'group' => $this->modelClass,
				'model' => $this->model
			)
		));
		if (!empty($this->data) && (count($attachments) < $limit)) {
			$friendly = explode('.', $this->data[$this->modelClass]['file']['name']);
			array_pop($friendly);
			$friendly = implode('.', $friendly);
			$this->data[$this->modelClass]['alternative'] = low($friendly);
			$this->data[$this->modelClass]['approved'] = false;
			if ($this->isAuthorized('attachments/approve')) {
				$this->data[$this->modelClass]['approved'] = true;
			}

			if ($this->{$this->modelClass}->save($this->data)) {
				$this->Session->setFlash('Upload successful.', 'flash'.DS.'success');
			}
			//failures will be handled by error messages, no need to set flash since they'll see an error (no ajax)
			//and ajax uploads handle the errors nicely
		}

		$this->set(compact('attachments', 'limit'));
	}

/**
 * Approves or denies an attachment. Attachments that are denied are deleted
 *
 * @param integer $id The attachment id
 * @param boolean $approve Whether or not to approve
 */
	public function approve($id = null, $approve = false) {
		$this->{$this->modelClass}->Behaviors->detach('Media.Coupler'); // don't require 'file' key
		if (!$id) {
			$this->cakeError('error404');
		} else {
			if ($approve) {
				$this->{$this->modelClass}->id = $id;
				if ($this->{$this->modelClass}->saveField('approved', true)) {
					$this->Session->setFlash('The upload request has been approved.', 'flash'.DS.'success');
				} else {
					$this->Session->setFlash('Unable to approve upload request. Please try again.', 'flash'.DS.'failure');
				}
			} else {
				$this->setAction('delete', $id);
			}
		}

		$this->redirect(array(
			'action' => 'approval',
			$this->model => $this->modelId
		));
	}

/**
 * Promotes or demotes a model's first image
 *
 * @param mixed $id The model's id
 * @param int $level The promotion level
 */
	public function promote($id = null, $level = 0) {
		$this->{$this->modelClass}->Behaviors->detach('Media.Coupler'); // don't require 'file' key
		if ($id) {
			$ids = array($id);
		} else {
			$ids = $this->_extractIds();
		}

		foreach ($ids as $id) {
			$attachment = $this->{$this->modelClass}->find('first', array(
				'fields' => array(
					'id'
				),
				'conditions' => array(
					'model' => $this->model,
					'group' => $this->modelClass,
					'foreign_key' => $id,
					'approved' => true
				)
			));
			if (!empty($attachment)) {
				$this->{$this->modelClass}->id = $attachment[$this->modelClass]['id'];
				$this->{$this->modelClass}->saveField('promoted', $level);
			}
		}

		if ($level == 1) {
			$msg = 'The selected Involvement Opportunities have been promoted.';
		} else {
			$msg = 'The selected Involvement Opportunities have been removed from the list of promoted items.';
		}

		$this->Session->setFlash($msg, 'flash'.DS.'success');
		$this->redirect($this->referer());
	}

/**
 * Deletes an attachment and removes the file.
 *
 * @param integer $id The id of the attachment
 */
	public function delete($id = null) {
		if (!$id) {
			$this->cakeError('error404');
		} else {
			if ($this->{$this->modelClass}->delete($id)) {
				$this->Session->setFlash('Your '.Inflector::humanize($this->modelKey).' has been deleted.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Unable to delete '.Inflector::humanize($this->modelKey).'. Please try agian.', 'flash'.DS.'failure');
			}
		}

		$this->redirect(array(
			'action' => 'index',
			$this->model => $this->modelId
		));
	}

/**
 * Returns the limit for this model and attachment type
 *
 * @param string model The name of the model
 * @param string modelClass The model class of the attachment (Image, Document)
 * @return integer Number of allowed attachments for the model
 */
	protected function _getLimit($model = null, $modelClass = null) {
		if (empty($model)) {
			$model = $this->model;
		}
		if (empty($modelClass)) {
			$modelClass = $this->modelClass;
		}
		$model = strtolower(Inflector::underscore($model));
		$modelClass = strtolower(Inflector::underscore($modelClass));
		$settingName = Inflector::pluralize($model).'.'.$model.'_'.$modelClass.'_limit';
		return Core::read($settingName) !== null ? Core::read($settingName) : 1;
	}
}

