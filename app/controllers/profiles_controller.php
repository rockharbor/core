<?php
/**
 * Profile controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Profiles Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class ProfilesController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Profiles';

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array('Formatting', 'SelectOptions', 'Media.Media');

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */
	function beforeFilter() {
		parent::beforeFilter();
		$this->_editSelf('view', 'edit');
	}

/**
 * Views a profile
 */
	function view() {
		// get user id
		if (isset($this->passedArgs['User'])) {
			$id = $this->passedArgs['User'];
		}
		$this->Profile->User->contain(array(
			'Profile',
			'Image',
			'ActiveAddress'
		));
		$profile = $this->Profile->User->read(null, $id);

		$this->set('profile', $profile);
		$this->set('campuses', $this->Profile->Campus->find('list'));
		$this->set('jobCategories', $this->Profile->JobCategory->find('list'));
		$this->set('elementarySchools', $this->Profile->ElementarySchool->find('list'));
		$this->set('middleSchools', $this->Profile->MiddleSchool->find('list'));
		$this->set('highSchools', $this->Profile->HighSchool->find('list'));
		$this->set('colleges', $this->Profile->College->find('list'));
		$this->set('classifications', $this->Profile->Classification->find('list'));
	}

/**
 * Edits a profile
 */
	function edit() {
		if (!$this->passedArgs['User'] && empty($this->data)) {
			$this->Session->setFlash('Invalid user');
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Profile->saveAll($this->data)) {
				$this->Session->setFlash('The user has been saved', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('The user could not be saved. Please, try again.', 'flash'.DS.'failure');
			}
		}
		
		$this->data = $this->Profile->find('first', array(
			'conditions' => array(
				'Profile.user_id' => $this->passedArgs['User']
			)
		));

		$this->set('publications', $this->Profile->User->Publication->find('list'));
		$this->set('campuses', $this->Profile->Campus->find('list'));
		$this->set('jobCategories', $this->Profile->JobCategory->find('list'));
		$this->set('elementarySchools', $this->Profile->ElementarySchool->find('list'));
		$this->set('middleSchools', $this->Profile->MiddleSchool->find('list'));
		$this->set('highSchools', $this->Profile->HighSchool->find('list'));
		$this->set('colleges', $this->Profile->College->find('list'));
		$this->set('classifications', $this->Profile->Classification->find('list'));
	}

/**
 * Administrates a user
 */
	function admin() {
		if (!$this->passedArgs['User'] && empty($this->data)) {
			$this->Session->setFlash('Invalid user');
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Profile->saveAll($this->data)) {
				$this->Session->setFlash('The user has been saved', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('The user could not be saved. Please, try again.', 'flash'.DS.'failure');
			}
		}
		$this->data = $this->Profile->find('first', array(
			'conditions' => array(
				'Profile.user_id' => $this->passedArgs['User']
			),
			'contain' => array(
				'User'
			)
		));
		$this->set('groups', $this->Profile->User->Group->findGroups($this->activeUser['Group']['id']));
	}

}
?>