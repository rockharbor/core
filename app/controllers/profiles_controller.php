<?php

class ProfilesController extends AppController {

	var $name = 'Profiles';
	
	var $helpers = array('Formatting', 'SelectOptions');
	
/**
 * Model::beforeFilter() callback
 *
 * Sets permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * Shows a list of profiles
 */ 
	function index() {
		$this->Profile->recursive = 0;
		$this->set('profiles', $this->paginate());
	}

/**
 * Deletes a profile
 */
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for profile', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Profile->delete($id)) {
			$this->Session->setFlash(__('Profile deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Profile was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>