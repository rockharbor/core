<?php
/**
 * Merge Request controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * MergeRequests Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class MergeRequestsController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'MergeRequests';

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array('Formatting');

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
 * Shows a list of merge requests
 */ 	
	function index() {
		// get model
		$Model = ClassRegistry::init($this->passedArgs['model']);

		$this->MergeRequest->recursive = 0;

		$this->MergeRequest->belongsTo['NewModel']['className'] = $this->passedArgs['model'];
		$this->MergeRequest->belongsTo['OriginalModel']['className'] = $this->passedArgs['model'];

		switch ($this->passedArgs['model']) {
			case 'User':
				$this->paginate = array(
					'conditions' => array(
						array('MergeRequest.model' => $this->passedArgs['model'])
					),
					'contain' => array(
						'NewModel' => array(
							'Address',
							'Profile'
						),
						'OriginalModel' => array(
							'Address',
							'Profile'
						)
					)
				);
			break;
		}

		$this->paginate['limit'] = 3;

		$this->set('model', $Model->alias);
		$this->set('displayField', $Model->displayField);
		$this->set('requests', $this->paginate());
	}

/**
 * Merge a request
 *
 * @param integer $id The id of the request to merge
 */
	function merge($id = null) {
		if (!$id) {
			$this->cakeError('error404');
		}
		
		// get request
		$request = $this->MergeRequest->read(null, $id);

		// get model we're merging
		$Model = ClassRegistry::init($request['MergeRequest']['model']);
		
		if (method_exists($Model, 'merge') && $Model->merge($request['MergeRequest']['merge_id'], $request['MergeRequest']['model_id'])) {
			$this->MergeRequest->delete($id);
			$this->Session->setFlash('Merge was successful.', 'flash'.DS.'success');
			
			$this->set('user', $Model->read(null, $request['MergeRequest']['merge_id']));
			$this->Notifier->notify(array(
				'to' => $request['MergeRequest']['merge_id'],
				'subject' => 'Your account has been activated',
				'template' => 'merge_requests_merge'
			), 'email');
		} else {
			$this->Session->setFlash('Unable to process request. Please try again.', 'flash'.DS.'failure');
		}
		
		$this->redirect(array(
			'action' => 'index',
			'model' => $request['MergeRequest']['model']
		));
	}

/**
 * Delete a request
 *
 * @param integer $id The id of the request to delete
 * @param boolean $ignore Whether or not to just ignore it
 */
	function delete($id = null, $ignore = 0) {		
		if (!$id) {
			$this->cakeError('error404');
		}
		
		// get request
		$request = $this->MergeRequest->read(null, $id);
		// get model we're merging
		$Model = ClassRegistry::init($request['MergeRequest']['model']);
		
		if ($ignore) {
			// activate new user
			$Model->id = $request['MergeRequest']['model_id'];
			if ($Model->hasField('active')) {
				$Model->saveField('active', true);
			}
			
			$this->set('user', $Model->read());
			$this->Notifier->notify(array(
				'to' => $request['MergeRequest']['model_id'],
				'subject' => 'Your account has been activated',
				'template' => 'merge_requests_merge'
			), 'email');
			
			$this->MergeRequest->delete($id);
			
			$this->Session->setFlash('Merge request was ignored.', 'flash'.DS.'success');
			$this->redirect(array('action'=>'index', 'model' => $request['MergeRequest']['model']));
		} else {
			// delete associated model target first
			if ($Model->delete($request['MergeRequest']['model_id'])) {
				// remove request
				$this->MergeRequest->delete($id);
				$this->Session->setFlash('Merge request was deleted.', 'flash'.DS.'success');
				$this->redirect(array('action'=>'index', 'model' => $request['MergeRequest']['model']));
			}
		}
		
		$this->Session->setFlash('Unable to process request. Please try again.', 'flash'.DS.'failure');
		$this->redirect(array('action' => 'index', 'model' => $request['MergeRequest']['model']));
	}

}
