<?php
/**
 * Queues controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       queue_email
 * @subpackage    queue_email.controllers
 */

/**
 * Queues Controller
 *
 * Allows management of queued emails
 *
 * @package       queue_email
 * @subpackage    queue_email.controllers
 */
class QueuesController extends QueueEmailAppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Queues';

/**
 * Additional components needed by this controller
 *
 * @var array
 */
	var $components = array(
		'Session',
		'QueueEmail.QueueEmail'
	);

/**
 * Additional helpers needed by this controller
 *
 * @var array
 */
	var $helpers = array(
		'Text'
	);

/**
 * Displays a list of the existing queue
 */
	function index() {
		$this->Queue->recursive = 0;
		$queues = $this->paginate();
		$queues = array_map(array($this->QueueEmail, 'interpret'), $queues);
		$this->set(compact('queues'));
	}

/**
 * Renders the output of the selected email
 *
 * @param string $id
 */
	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid queue', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('queue', $this->QueueEmail->interpret($this->Queue->read(null, $id)));
	}

/**
 * Deletes a message from the queue
 *
 * @param string $id
 */
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for queue', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Queue->delete($id)) {
			$this->Session->setFlash(__('Queue deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Queue was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>