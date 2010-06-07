<?php
class PaymentOptionsController extends AppController {

	var $name = 'PaymentOptions';
	
	var $helpers = array('SelectOptions', 'Formatting');
	
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