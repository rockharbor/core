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
				$this->Session->setFlash(__('The payment option has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The payment option could not be saved. Please, try again.', true));
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
			$this->Session->setFlash(__('Invalid payment option', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->PaymentOption->save($this->data)) {
				$this->Session->setFlash(__('The payment option has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The payment option could not be saved. Please, try again.', true));
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
			$this->Session->setFlash(__('Invalid id for payment option', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->PaymentOption->delete($id)) {
			$this->Session->setFlash(__('Payment option deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Payment option was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>