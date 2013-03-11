<?php
/**
 * Alert controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Alerts Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class AlertsController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	public $name = 'Alerts';

/**
 * Extra components for this controller
 *
 * @var array
 */
	public $components = array('MultiSelect.MultiSelect');

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	public $helpers = array('MultiSelect.MultiSelect', 'Formatting');

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 */
	public function beforeFilter() {
		parent::beforeFilter();
	}

/**
 * List of all Alerts
 */
	public function index() {
		$this->Alert->recursive = 0;
		$alerts = $this->paginate();
		foreach ($alerts as &$alert) {
			$count = $this->Alert->AlertsUser->find('count', array(
				'conditions' => array(
					'alert_id' => $alert['Alert']['id']
				)
			));
			$alert['Alert']['read_by_users'] = $count;
		}
		$this->set(compact('alerts'));
	}

/**
 * Views an alert
 *
 * @param integer $id The id of the alert to read
 */
	public function view($id = null) {
		$groups = $this->Alert->Group->findGroups($this->activeUser['Group']['id']);
		$alert = $this->Alert->find('first', array(
			'conditions' => array(
				'Alert.id' => $id,
				'Alert.group_id' => $groups
			)
		));

		if (empty($alert)) {
			$this->cakeError('error404');
		}

		$this->set(compact('alert'));
	}

/**
 * History of alerts for a user
 *
 * @param string $unread Search filter (`read`, `unread`, empty for all)
 */
	public function history($unread = '') {
		$userId = $this->activeUser['User']['id'];
		$groups = $this->Alert->Group->findGroups($this->activeUser['Group']['id']);

		switch ($unread) {
			case 'unread':
			$this->paginate = array(
				'conditions' => array(
					'Alert.id' => $this->Alert->getUnreadAlerts($userId, $groups)
				),
				'order' => 'Alert.created DESC'
			);
			break;
			case 'read':
			$this->paginate = array(
				'conditions' => array(
					'Alert.id' => $this->Alert->getReadAlerts($userId)
				),
				'order' => 'Alert.created DESC'
			);
			break;
			default:
			$this->paginate = array(
				'conditions' => array(
					'Alert.group_id' => $groups
				),
				'order' => 'Alert.created DESC'
			);
			break;
		}

		$this->MultiSelect->saveSearch($this->paginate);
		$this->set('read', $this->Alert->getReadAlerts($userId));
		$this->set('alerts', $this->paginate());
	}

/**
 * Marks an alert as `read` for the user
 *
 * @param integer $id The alert id
 */
	public function read($id = null) {
		$userId = $this->activeUser['User']['id'];

		if ($id) {
			$ids = array($id);
		} else {
			$ids = $this->_extractIds($this->Alert, '/Alert/user_id');
		}

		foreach ($ids as $id) {
			$this->Alert->markAsRead($userId, $id);
		}

		$this->redirect(array('action' => 'history', 'both'));
	}

/**
 * Adds an Alert
 */
	public function add() {
		if (!empty($this->data)) {
			$this->Alert->create();
			if ($this->Alert->save($this->data)) {
				$this->Session->setFlash('This alert has been created.', 'flash'.DS.'success');
				$this->redirect(array('action' => 'index'), null, null, true);
			} else {
				$this->Session->setFlash('Unable to create alert. Please try again.', 'flash'.DS.'failure');
			}
		}

		$this->set('groups', $this->Alert->Group->find('list',
			array(
				'conditions' => array(
					'Group.conditional' => false
				)
			)
		));
	}

/**
 * Edits an Alert
 */
	public function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->cakeError('error404');
		}
		if (!empty($this->data)) {
			if ($this->Alert->save($this->data)) {
				$this->Session->setFlash('This alert has been modified.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Unable to update alert. Please try again.', 'flash'.DS.'failure');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Alert->read(null, $id);
		}

		$this->set('groups', $this->Alert->Group->find('list',
			array(
				'conditions' => array(
					'Group.conditional' => false
				)
			)
		));
	}

/**
 * Deletes an Alert
 */
	public function delete($id = null) {
		if (!$id) {
			$this->cakeError('error404');
		}
		if ($this->Alert->delete($id)) {
			$this->Session->setFlash('This alert has been deleted.', 'flash'.DS.'success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('Unable to delete alert. Please try again.', 'flash'.DS.'failure');
		$this->redirect(array('action' => 'index'));
	}
}
