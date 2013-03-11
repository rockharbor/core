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
	public $name = 'Profiles';

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	public $helpers = array('Formatting', 'SelectOptions', 'Media.Media');

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->_editSelf('view', 'edit');
	}

/**
 * Views a profile
 */
	public function view() {
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
		$this->set('promoted', $this->Profile->User->Image->find('promoted'));
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
	public function edit() {
		if (!$this->passedArgs['User'] && empty($this->data)) {
			$this->cakeError('error404');
		}
		if (!empty($this->data)) {
			if ($this->Profile->saveAll($this->data)) {
				$this->Session->setFlash('This user has been saved.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Unable to save this user. Please try again.', 'flash'.DS.'failure');
			}
		}

		$this->data = $this->Profile->find('first', array(
			'conditions' => array(
				'Profile.user_id' => $this->passedArgs['User']
			)
		));

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
	public function admin() {
		if (!$this->passedArgs['User'] && empty($this->data)) {
			$this->cakeError('error404');
		}
		if (!empty($this->data)) {
			if ($this->Profile->saveAll($this->data)) {
				$this->Session->setFlash('This. user has been saved.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Unable to save this user. Please try again.', 'flash'.DS.'failure');
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
		$groups = $this->Profile->User->Group->find('list', array(
			'conditions' => array(
				'Group.conditional' => false
			)
		));
		$this->set('groups', $groups);
	}

}
