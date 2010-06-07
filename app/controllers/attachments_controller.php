<?php

/**
 * Controller for attachments.
 *
 * Extended by Documents and Images controllers.
 *
 * @author Jeremy Harris <jharris@rockharbor.org>
 */
class AttachmentsController extends AppController {

/**
 * Force the child (documents/images) to use this view path
 *
 * @var string viewPath
 */
	var $viewPath = 'attachments';
	
	var $helpers = array(
		'Media.Media',
		'Number'
	);
	
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
		$this->modelClass = Inflector::singularize($this->name);
	}
	
	function beforeRender() {
		parent::beforeRender();

		$this->set('attachmentModel', $this->modelClass);
		$this->set('model', $this->model);
		$this->set('modelId', $this->modelId);
	}
 
	/*function beforeFilter() {
		parent::beforeFilter();
		
		// for accessing the model
		$this->set('model', $this->modelClass);
		// for printing it out all friendly-like
		$this->set('modelKey', $this->modelKey);
		
		// give the views the schema, too, so they can
		// create forms with slight differences
		$schema = $this->{$this->modelClass}->schema();
		$this->set('schema', $schema);

		$this->set('title_for_layout', Inflector::pluralize(Inflector::humanize($this->modelKey)));
		
		if ($this->Auth->user()) {
			$this->Auth->allow('index','download','upload','delete');
		}
	}	*/
	

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
			'mimeType' => array($ext => $attachment[$this->modelClass]['mime_type']),
			'path' => MEDIA.$attachment[$this->modelClass]['dirname'].DS
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
		if (!empty($this->data)) {			
			$this->data[$this->model]['id'] = $this->modelId;
			$this->data[$this->modelClass][0]['model'] = $this->model;
			$this->data[$this->modelClass][0]['foreign_key'] = $this->modelId;
			$this->data[$this->modelClass][0]['group'] = $this->modelClass;
			$friendly = explode('.', $this->data[$this->modelClass][0]['file']['name']);
			array_pop($friendly);
			$friendly = implode('.', $friendly);
			$this->data[$this->modelClass][0]['alternative'] = low($friendly);			
			$this->{$this->modelClass}->unbindModel(array('belongsTo' => array($this->model)));
			/* 
			save all was breaking it, but save worked so let's move the data
			into the right place
			*/			
			$this->data[$this->modelClass] = $this->data[$this->modelClass][0];
			
			if ($this->{$this->modelClass}->save($this->data, array('validate' => 'first'))) {
				$this->Session->setFlash(Inflector::humanize($this->modelKey).' added!', 'flash_success');
			}
		}
	}
	
/**
 * Deletes an attachment and removes the file.
 *
 * @param integer $id The id of the attachment
 */ 
	function delete($id = null) {		
		if (!$id) {
			$this->Session->setFlash('Invalid id', 'flash_failure');
		} else {		
			if ($this->{$this->modelClass}->delete($id)) {
				$this->Session->setFlash(Inflector::humanize($this->modelKey).' deleted', 'flash_success');
			} else {
				$this->Session->setFlash(Inflector::humanize($this->modelKey).' was not deleted', 'flash_failure');
			}	
		}
		
		$this->redirect(array(
			'action' => 'index',
			$model => $this->modelId
		));	
	}
}


?>