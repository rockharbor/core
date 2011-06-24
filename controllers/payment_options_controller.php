<?php
/**
 * Payment Option controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * PaymentOptions Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class PaymentOptionsController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'PaymentOptions';

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array('SelectOptions', 'Formatting');
	
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
 * Displays a list of payment options for the chosen involvement
 */ 
	function index() {
		$this->PaymentOption->recursive = 0;
		$this->set('paymentOptions', $this->paginate(array(
			'involvement_id' => $this->passedArgs['Involvement']
		)));
		
		$this->set('involvementId', $this->passedArgs['Involvement']);
	}

/**
 * Adds a payment option
 */ 
	function add() {
		if (!empty($this->data)) {
			$this->PaymentOption->create();
			if ($this->PaymentOption->save($this->data)) {
				$this->Session->setFlash('This payment option has been created.', 'flash'.DS.'success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Unable to create payment option. Please try again.', 'flash'.DS.'failure');
			}
		}
		
		$this->set('involvementId', $this->passedArgs['Involvement']);
	}

/**
 * Edits a payment option
 *
 * @param integer $id The id of the payment option to edit
 */ 
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			//404
			$this->Session->setFlash(__('Invalid payment option', true), 'flash'.DS.'failure');
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->PaymentOption->save($this->data)) {
				$this->Session->setFlash('This payment option has been saved.', 'flash'.DS.'success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Unable to save this payment option. Please try again.', 'flash'.DS.'failure');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->PaymentOption->read(null, $id);
		}
	}

/**
 * Deletes a payment option
 *
 * @param integer $id The id of the payment option to delete
 */ 
	function delete($id = null) {
		if (!$id) {
			//404
			$this->Session->setFlash(__('Invalid id for payment option', true), 'flash'.DS.'failure');
			$this->redirect(array('action'=>'index'));
		}
		if ($this->PaymentOption->delete($id)) {
			$this->Session->setFlash('This payment option has been deleted.', 'flash'.DS.'success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('Unable to delete this payment option.', 'flash'.DS.'failure');
		$this->redirect(array('action' => 'index'));
	}
}
?>