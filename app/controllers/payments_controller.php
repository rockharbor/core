<?php
/**
 * Payment controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Payments Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class PaymentsController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Payments';
	
/**
 * Extra components for this controller
 *
 * @var array
 */
	var $components = array('MultiSelect.MultiSelect', 'AuthorizeDotNet');

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array('Text', 'Formatting');

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
 * Shows a list of payments made by a User
 */
	function index() {
		if (!isset($this->passedArgs['User'])) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect($this->referer());
		}
		
		$userId = $this->passedArgs['User'];
		
		$this->paginate = array(
			'conditions' => array(
				'or' => array(
					'Payment.user_id' => $userId,
					'Payment.payment_placed_by' => $userId
				)
			),
			'contain' => array(
				'Roster' => array(	
					'Involvement'
				),
				'PaymentType',
				'User' => array(
					'Profile'
				),
				'Payer' => array(
					'Profile'
				),
				'PaymentOption'
			)
		);
		
		$this->set('payments', $this->paginate());
	}

/**
 * Adds a payment to a search from a MultiSelect key
 *
 * @param string $mskey The MultiSelect cache key to pull a list from
 */
	function add($mskey) {
		// get selected
		$search = $this->MultiSelect->getSearch($mskey);
		$search['contain']['User'] = array('Profile');		
		$selected = $this->MultiSelect->getSelected($mskey);
		// assume they want all if they didn't select any
		if (!empty($selected)) {
			$search['conditions']['Roster.id'] = $selected;
		} 
		$users = $this->Payment->Roster->find('all', $search);
		$involvement = $this->Payment->Roster->Involvement->find('first', array(
			'conditions' => array(
				'Involvement.id' => $this->passedArgs['Involvement']
			),
			'contain' => array(
				'InvolvementType'
			)
		));
		
		// bind CreditCard to process the card 
		$this->Payment->bindModel(array(
			'hasOne' => array(
				'CreditCard' => array(	
					'foreignKey' => false
				)
			)
		));
		
		if (!empty($this->data)) {
			$paymentType = $this->Payment->PaymentType->read(array('name'), $this->data['Payment']['payment_type_id']);
			
			$payForUsers = Set::extract('/Roster[balance>0]/..', $users);
			
			// get balance
			$balance = Set::apply('/Roster/balance', $payForUsers, 'array_sum');
			
			$amount = $this->data['Payment']['amount'];		
			
			// set `amount` validation rule to reflect balance range and validate as it's
			// own field because we'll be splitting the payments up
			if ($amount <= 0 || $amount > $balance) {
				$this->Payment->invalidate('amount', 'Your chosen amount must be at or under $'.$balance.'.');
			}
				
			// assuming all users still have a balance
			$avg = round($amount/count($payForUsers), 2);
			
			// build payment records (transaction id to be added later)
			$payments = array();
			foreach ($payForUsers as $user) {
				// get amount they can receive
				$amt = ($avg <= $user['Roster']['balance'] ? $avg : $user['Roster']['balance']);
				// if less than the average, get the amount left to distribute elsewhere
				$amount -= $amt;
				// saving balance in here as well, to be removed later
				$payments[] = array(
					'balance' => $user['Roster']['balance']-$amt,
					'user_id' => $user['Roster']['user_id'],
					'roster_id' => $user['Roster']['id'],
					'amount' => $amt,
					'payment_type_id' => $this->data['Payment']['payment_type_id'],
					'payment_placed_by' => $this->activeUser['User']['id'],
					'payment_option_id' => $user['Roster']['payment_option_id']
				);
				
				// to associate with invoice number
				$paymentOption = $this->Payment->PaymentOption->read(null, $user['Roster']['payment_option_id']);
			}			
			
			// if there was any left over, distribute to other users, otherwise, remove unwanted field
			foreach ($payments as &$payment) {
				if ($amount > 0) {
					// get and then set the amount they can receive
					$amt = ($amount <= $payment['balance'] ? $amount : $payment['balance']);				
					$payment['amount'] += $amt;				
					$amount -= $amt;
				}
				
				unset($payment['balance']);
			}
			
			// create extra fields for credit card
			$verb = count($payForUsers) > 1 ? 'have' : 'has';
			$pVerb = count($payForUsers) > 1 ? 'had payments' : 'made a payment';
			// comma's and the like are not permitted by Authorize.net
			$description = implode(' and ', Set::extract('/User/Profile/name', $payForUsers)).' '.$verb.' '.$pVerb.' made for '.$involvement['InvolvementType']['name'].' '.$involvement['Involvement']['name'];
			$this->data['CreditCard']['invoice_number'] = $paymentOption['PaymentOption']['account_code'];
			$this->data['CreditCard']['description'] = $description;
			$this->data['CreditCard']['email'] = $this->activeUser['Profile']['primary_email'];			
			$this->data['CreditCard']['amount'] = $this->data['Payment']['amount'];	
			
			// make sure all the fields validate before charging the card, if there is one
			if (empty($this->Payment->validationErrors) && $this->Payment->saveAll($payments, array('validate' => 'only'))) {
				$isCreditCard = (isset($this->_data['CreditCard']) && isset($this->_data['CreditCard']['credit_card_number']));
				
				// next, make sure the credit card gets authorized
				if ($isCreditCard) {
					$pValidates = $this->Payment->CreditCard->save($this->data['CreditCard']);
				} else {
					// no extra validation for other payment types
					$pValidates = true;
				}
				
				if ($pValidates) {
					// credit card as been charged, save the payment record
					$this->Payment->create();
					foreach ($payments as &$completePayment) {						
						if ($isCreditCard) {
							// credit card
							$completePayment['number'] = substr($this->data['CreditCard']['credit_card_number'], -4);
							$completePayment['transaction_id'] = $this->Payment->CreditCard->transactionId;
						} elseif (isset($this->data['Payment']['number'])) {
							// other
							$completePayment['number'] = $this->data['Payment']['number'];
						} 
					}
					
					$this->Payment->saveAll($payments, array('validate' => false));
			
					$verb = count($payments) > 1 ? 'payments' : 'a payment';
					App::import('Helper', 'Text');
					$Text = new TextHelper();
					$this->Session->setFlash('You\'ve made '.$verb.' for '.$Text->toList(Set::extract('/User/Profile/name', $payForUsers)).'!', 'flash_success');
				} else {
					$this->Session->setFlash('Error processing payment. '.$this->Payment->CreditCard->creditCardError, 'flash_failure');						
				}					
			} else {
				$this->Session->setFlash('Could not process payment.', 'flash_failure');
			}			
		}
		
		// get user addresses for js
		$userAddresses = $this->Payment->User->Address->find('all', array(
			'conditions' => array(
				'foreign_key' => $this->activeUser['User']['id'],
				'model' => 'User'
			)
		));
		// format for select
		$addresses = Set::combine($userAddresses, '/Address/id', '/Address/name');
		
		$paymentTypes = $this->Payment->PaymentType->find('list');
		
		$this->set(compact('involvement', 'users', 'userAddresses', 'addresses', 'paymentTypes', 'mskey'));
		
	}

/**
 * Deletes a payment
 *
 * @param integer $id The id of the payment to delete
 */
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for payment', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Payment->delete($id)) {
			$this->Session->setFlash(__('Payment deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Payment was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>