<?php
/**
 * Covenant controller class.
 *
 * @copyright     Copyright 2014, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Covenants Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class CovenantsController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	public $name = 'Covenants';

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	public $helpers = array('Formatting', 'SelectOptions');

/**
 * Model::beforeFilter() callback
 */
	public function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * Model::beforeRender() callback
 */
	public function beforeRender() {
		parent::beforeRender();
	}

/**
 * Displays a list of covenants
 */
	public function index() {
		$this->set('covenants', $this->Covenant->find('all', array(
			'conditions' => array(
				'user_id' => $this->passedArgs['User']
			)
		)));
		$this->set('userId', $this->passedArgs['User']);
	}

/**
 * Adds a covenant
 */
	public function add() {
		if (!empty($this->data)) {
			$this->Covenant->create();
			if ($this->Covenant->save($this->data)) {
				$this->Session->setFlash(__('This covenant has been created.', true), 'flash'.DS.'success');
			} else {
				$this->Session->setFlash(__('Unable to create covenant. Please try again.', true), 'flash'.DS.'failure');
			}
		}

		$this->set('userId', $this->passedArgs['User']);
	}

/**
 * Deletes a covenant
 *
 * @param integer $id The id of the covenant to delete
 */
	public function delete($id = null) {
		$user = isset($this->passedArgs['User']) ? $this->passedArgs['User'] : null;
		if (!$id) {
			$this->cakeError('error404');
		}
		if ($this->Covenant->delete($id)) {
			$this->Session->setFlash(__('This covenant has been deleted.', true), 'flash'.DS.'success');
			$this->redirect(array('action'=>'index', 'User' => $user));
		}
		$this->Session->setFlash(__('Unable to delete covenant. Please try again.', true), 'flash'.DS.'failure');
		$this->redirect(array('action' => 'index', 'User' => $user));
	}
}
