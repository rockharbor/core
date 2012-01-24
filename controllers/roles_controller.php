<?php
/**
 * Roles controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Roles Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class RolesController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Roles';

/**
 * Helpers
 *
 * @var array
 */
	var $helpers = array(
		'Formatting'
	);
	
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
 * List of roles for a ministry
 * 
 * Note: Only shows roles of this ministry
 */
	function index() {
		$this->Role->recursive = -1;
		
		$this->paginate = array(
			'conditions' => array(
				'Role.ministry_id' => $this->passedArgs['Ministry']
			)
		);
		$this->set('roles', $this->paginate());
		$this->set('ministry_id', $this->passedArgs['Ministry']);
	}

/**
 * Adds a role to a ministry
 */
	function add() {
		if (!empty($this->data)) {
			$this->Role->create();
			if ($this->Role->save($this->data)) {
				$this->Session->setFlash('This Role has been created.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Unable to create this role. Please try again.', 'flash'.DS.'failure');
			}
		}
		$this->set('ministry', $this->Role->Ministry->read(null, $this->passedArgs['Ministry']));
	}

/**
 * Edits a role
 */
	function edit($id) {
		if (!$id && empty($this->data)) {
			$this->cakeError('error404');
		}
		if (!empty($this->data)) {
			if ($this->Role->save($this->data)) {
				$this->Session->setFlash('This role has been saved.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Unable to save this role. Please try again.', 'flash'.DS.'failure');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Role->read(null, $id);
		}
	}

/**
 * Deletes a record for this model
 *
 * @param integer $id The id of the model
 */
	function delete($id = null) {
		if (!$id) {
			$this->cakeError('error404');
		}
		if ($this->Role->delete($id)) {
			$this->Session->setFlash('This role has been deleted.', 'flash'.DS.'success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('Unable to delete this role. Please try again.', 'flash'.DS.'failure');
		$this->redirect(array('action' => 'index'));
	}

}
?>