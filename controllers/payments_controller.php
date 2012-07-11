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
	var $helpers = array('MultiSelect.MultiSelect', 'Text', 'Formatting');

/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		$this->_editSelf('index', 'view');
		parent::beforeFilter();
	}

/**
 * Shows a single payment
 *
 * @param integer $id The id of the payment
 */
	function view($id) {
		$payment = $this->Payment->find('first', array(
			'conditions' => array(
				'Payment.id' => $id
			),
			'contain' => array(
				'Roster' => array(
					'Involvement' => array(
						'fields' => array('name')
					)
				),
				'PaymentType',
				'User' => array(
					'Profile' => array(
						'fields' => array('name')
					)
				),
				'Payer' => array(
					'Profile' => array(
						'fields' => array('name')
					)
				),
			)
		));
		$this->set('payment', $payment);
	}

/**
 * Shows a list of payments made by a User
 */
	function index() {
		if (!isset($this->passedArgs['User'])) {
			$this->cakeError('error404');
		}
		
		$userId = $this->passedArgs['User'];

		$this->paginate = array(
			'conditions' => array(
				'Payment.user_id' => $userId
			),
			'contain' => array(
				'Roster' => array(	
					'Involvement' => array(
						'fields' => array('id', 'name')
					)
				),
				'PaymentType',
				'User' => array(
					'Profile' => array(
						'fields' => array('user_id', 'name')
					)
				),
				'Payer' => array(
					'Profile' => array(
						'fields' => array('user_id', 'name')
					)
				),
			)
		);

		if (isset($this->passedArgs['Roster'])) {
			$this->paginate['conditions']['Roster.id'] = $this->passedArgs['Roster'];
		}

		$this->MultiSelect->saveSearch($this->paginate);
		
		$this->set('payments', $this->paginate());
		$this->Payment->User->contain(array('Profile'));
		$this->set('user', $this->Payment->User->read(null, $userId));
	}

/**
 * Adds a payment to a search from a MultiSelect key
 *
 * @param string $mskey The MultiSelect cache key to pull a list from
 */
	function add($mskey) {
		// check to see if this is a MultiSelect
		if ($this->MultiSelect->check($mskey)) {
			$ids = $this->MultiSelect->getSelected($mskey);
		} else {
			$ids = array($mskey);
		}
		// get selected
		$users = $this->Payment->Roster->find('all', array(
			'conditions' => array(
				'Roster.id' => $ids,
			),
			'contain' => array(
				'User' => array(
					'Profile'
				)
			)
		));
		
		$involvement = $this->Payment->Roster->Involvement->find('first', array(
			'conditions' => array(
				'Involvement.id' => $users[0]['Roster']['involvement_id']
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
				);
				
				// to associate with invoice number
				$paymentOption = $this->Payment->Roster->PaymentOption->read(null, $user['Roster']['payment_option_id']);
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
				$isCreditCard = (isset($this->data['CreditCard']) && isset($this->data['CreditCard']['credit_card_number']));
				
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
					
					$this->set('involvement', $involvement);
					$this->set('payer', $this->activeUser);
					$this->set('amount', $this->data['Payment']['amount']);
					$leaders = $this->Payment->Roster->Involvement->getLeaders($involvement['Involvement']['id']);
					
					$subject = $this->activeUser['Profile']['name'].' made a payment for '.$involvement['Involvement']['name'];
					
					foreach ($leaders as $leader) {
						$this->Notifier->notify(array(
							'to' => $leader,
							'template' => 'payments_add_leader',
							'subject' => $subject
						));
					}
			
					$this->Session->setFlash('Your payment has been received.', 'flash'.DS.'success');
				} else {
					$this->Session->setFlash('Unable to process payment. '.$this->Payment->CreditCard->creditCardError, 'flash'.DS.'failure');
				}					
			} else {
				$this->Session->setFlash('Unable to process payment. Please try again.', 'flash'.DS.'failure');
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
		
		$paymentTypes = $this->Payment->PaymentType->find('all', array(
			'conditions' => array(
				'group_id' => $this->Payment->PaymentType->Group->findGroups($this->activeUser['Group']['id'])
			)
		));
		$types = array_unique(Set::extract('/PaymentType/type', $paymentTypes));
		$types = array_intersect_key($this->Payment->PaymentType->types, array_flip($types));
		
		$this->set(compact('involvement', 'users', 'userAddresses', 'addresses', 'paymentTypes', 'types', 'mskey'));
		
	}
	
/**
 * Edits a payment
 *
 * @param integer $id The id of the payment to edit
 */
	function edit($id = null) {
		if (!$id) {
			$this->cakeError('error404');
		}
		debug($this->data);
		if (!empty($this->data)) {
			if ($this->Payment->save($this->data)) {
				$this->Session->setFlash('This payment has been saved.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Unable to edit this payment. Please, try again.', 'flash'.DS.'failure');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Payment->read(null, $id);
		}
		$paymentTypes = $this->Payment->PaymentType->find('list', array(
			'conditions' => array(
				'group_id' => $this->Payment->PaymentType->Group->findGroups($this->activeUser['Group']['id'])
			)
		));
		$this->set(compact('paymentTypes'));
	}

/**
 * Deletes a payment
 *
 * @param integer $id The id of the payment to delete
 */
	function delete($id = null) {
		if (!$id) {
			$this->cakeError('error404');
		}
		if ($this->Payment->delete($id)) {
			$this->Session->setFlash('This payment has been deleted.', 'flash'.DS.'success');
			$this->redirect(array(
					'controller' => 'pages',
					'action' => 'message'
				));
		}
		$this->Session->setFlash('Unable to delete this payment. Please try again.', 'flash'.DS.'failure');
		$this->redirect(array(
			'controller' => 'pages',
			'action' => 'message'
		));
	}
}
?>