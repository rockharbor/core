<?php
/**
 * Roster controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Rosters Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class RostersController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Rosters';

/**
 * Extra components for this controller
 *
 * @var array
 */
	var $components = array('FilterPagination', 'AuthorizeDotNet', 'MultiSelect');

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array('Formatting', 'MultiSelect');
	
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
	
/** Displays a roster list
 *
 * ### Passed args:
 * - integer $Involvement The id of the involvement to filter for
 * - integer $User The id of the user to filter for
 */ 
	function index() {
		$conditions = array();
		$userConditions = array();
		$profileConditions = array();
		
		// if involvement is defined, show just that involvement
		if (isset($this->passedArgs['Involvement'])) {
			$conditions['Roster.involvement_id'] = $this->passedArgs['Involvement'];
		}	
		
		// if user is defined, show just from that user's list of people they can sign up
		if (isset($this->passedArgs['User'])) {
			$userConditions = array('User.id' => $this->passedArgs['User']);
		}
		
		$involvement = $this->Roster->Involvement->read(null, $this->passedArgs['Involvement']);
		
		/*$profileConditions = array(
			'or' => array(
				'Profile.household_contact_signups' => true,
				'Profile.child' => true
			)
		);*/
		
		// get roster ids
		$roster = $this->Roster->find('all', compact('conditions'));
		$rosterIds = Set::extract('/Roster/user_id', $roster);
		
		// get user and household ids
		$user = $this->Roster->User->find('first', array(
			'conditions' => $userConditions,
			'contain' => array(
				'Profile',
				'HouseholdMember' => array(
					'Household' => array(
						'HouseholdMember' => array(
							'User' => array(
								'Profile' => array(
									'conditions' => $profileConditions
								)
							)
						)
					)				
				),
				'Group'
			)
		));		
		$userIds = Set::extract('/HouseholdMember/Household/HouseholdMember/User/id', $user);
		
		if (isset($this->passedArgs['User'])) {
			$conditions['User.id'] = array_intersect($rosterIds, $userids);
		} else {
			$conditions['User.id'] = $rosterIds;
		}
		
		$contain = array(
			'User' => array(
				'Profile'
			),
			'Involvement' => array(
				'InvolvementType'
			),
			'Role',
			'PaymentOption',
			'Parent',
			'RosterStatus',
			'Payment'
		);
		
		$this->Roster->recursive = 0;
		$this->paginate = compact('conditions','contain');
		
		// save search for multi select actions
		$this->MultiSelect->saveSearch($this->paginate);
		
		// set based on criteria
		$this->set('canCheckAll', !isset($this->passedArgs['User']));
		$this->set('rosters', $this->paginate());
		$this->set('householdIds', $userIds);
		$this->set('rosterIds', $rosterIds);
		$this->set('involvement', $involvement);
		$this->set('involvementId', $this->passedArgs['Involvement']);
	}

/**
 * Shows involvement history
 *
 * ### Passed args:
 * - integer $User The id of the user
 *
 * @param string $passed 'passed' to show opportunities that have passed
 */ 	
	function involvement($passed = '') {
		if (isset($this->passedArgs['User'])) {
			$userId = $this->passedArgs['User'];
		} else {
			$userId = $this->activeUser['User']['id'];
		}
		
		if (!$userId) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect($this->referer());
		}
		
		$conditions['Roster.user_id'] = $userId;
		if ($passed != 'passed') {
			$conditions[$this->Roster->Involvement->getVirtualField('passed')] = 0;
		}
		
		$this->paginate = array(
			'conditions' => $conditions,
			'contain' => array(
				'User',
				'Involvement' => array(
					'Date'
				)
			)
		);
		
		$this->set('passed', $passed);
		$this->set('userId', $userId);
		$this->set('rosters', $this->paginate());
	}

/**
 * Signs a user up for an involvement opportunity
 *
 * Checks payment information, if needed. Creates childcare records, Roster records,
 * Payment records and runs credit cards.
 *
 * ### Passed args:
 * - integer `User` The (main) user id to sign up
 * - integer `Involvement` The involvement opportunity
 */
	function add() {
		$userId = isset($this->passedArgs['User']) ? $this->passedArgs['User'] : null;
		$involvementId = isset($this->passedArgs['Involvement']) ? $this->passedArgs['Involvement'] : null;
		
		if (!$userId || !$involvementId) {
			$this->Session->setFlash('Invalid id', 'flash_failure');
			$this->redirect(array('action'=>'index'));
		}
		
		// bind faux-model to make use of validation
		$this->Roster->bindModel(array(
			'hasOne' => array(
				'CreditCard' => array(	
					'foreignKey' => false
				)
			)
		));
		
		// get roster ids for comparison (to see if they're signed up)
		$involvementRoster = $this->Roster->find('list', array(
			'conditions' => array(
				'Roster.id',
				'Roster.involvement_id' => $involvementId
			),
			'fields' => array(
				'Roster.id',
				'Roster.user_id'
			),
			'contain' => false
		));
		// get needed information about the user and this involvement
		$involvement = $this->Roster->Involvement->read(null, $involvementId);	
		
		// can't sign up for inactive involvements
		if (!$involvement['Involvement']['active']) {
			$this->Session->setFlash('You cannot sign up for an inactive event.', 'flash_failure');
			$this->redirect($this->emptyPage);
		}
		
		// get user info and all household info where they are the contact
		$user = $this->Roster->User->find('first', array(
			'conditions' => array(
				'User.id' => $userId
			),
			'contain' => array(
				'Profile',
				'HouseholdMember' => array(
					'Household' => array(
						'HouseholdMember' => array(
							'conditions' => array(
								'not' => array(
									'HouseholdMember.user_id' => $involvementRoster
								)
							),
							'User' => array(
								'Profile' => array(
									'conditions' => array(
										'or' => array(
											'Profile.household_contact_signups' => true,
											'Profile.child' => true
										)
									)
								)
							)
						)
					)				
				),
				'Group'
			)
		));
				
		// they're submitting the form
		if (!empty($this->data)) {
			// first things we'll do is validate all the data. if it all validates, we'll try to
			// process the credit card. if the credit card goes through, we'll add everyone to the 
			// roster (including childcare) and save the payment info
			
			// get confirm status
			$confirmStatus = $this->Roster->RosterStatus->findByName('Confirmed');
			
			// get chosen payment option 
			if ($involvement['Involvement']['take_payment']) {
				$paymentOption = $this->Roster->PaymentOption->read(null, $this->data['Default']['payment_option_id']);
				$paymentType = $this->Roster->Payment->PaymentType->findByName('Credit Card');
			}
			
			// extract info to check/save for roster
			$rValidates = true;
			$this->Roster->_validationErrors = array();
			
			foreach ($this->data['Roster'] as $roster => &$values) {
				// set defaults
				$values['Roster']['involvement_id'] = $this->data['Default']['involvement_id'];
				$values['Roster']['roster_status_id'] = $confirmStatus['RosterStatus']['id'];				
				if (isset($this->data['Default']['payment_option_id'])) {
					$values['Roster']['payment_option_id'] = $this->data['Default']['payment_option_id'];
				}
				if (isset($this->data['Default']['role_id'])) {
					$values['Roster']['role_id'] = $this->data['Default']['role_id'];
				}
			
				// they weren't checked, remove associated data (Answers)
				if (!isset($values['Roster']) || !isset($values['Roster']['user_id'])) {
					unset($this->data['Roster'][$roster]);
				} else {
					// only add a payment if we're taking one
					if ($involvement['Involvement']['take_payment'] && $this->data['Default']['payment_option_id'] > 0 && isset($this->data['CreditCard'])) {
						$amount = $this->data['PaymentOption']['pay_deposit_amount'] ? $paymentOption['PaymentOption']['deposit'] : $paymentOption['PaymentOption']['total'];
						
						// add payment record to be saved (transaction id to be added later)
						$values['Payment'] = array(
							'0' => array(
								'user_id' => $values['Roster']['user_id'],
								'amount' => $amount,
								'payment_type_id' => $paymentType['PaymentType']['id'],
								'number' => substr($this->data['CreditCard']['credit_card_number'], -4),
								'payment_placed_by' => $this->activeUser['User']['id'],
								'payment_option_id' => $this->data['Default']['payment_option_id'],
								'comment' => $this->data['CreditCard']['first_name'].' '.$this->data['CreditCard']['last_name'].'\'s card processed by '.$this->activeUser['Profile']['name'].'.'
							)
						);
					}
				
					// save validate success only if we haven't failed yet (so not to overwrite a failure)
					if ($rValidates) {
						$rValidates = $this->Roster->saveAll($values, array('validate' => 'only'));						
					} else {
						// still validate this roster to generate errors
						$this->Roster->saveAll($values, array('validate' => 'only'));
					}
					
					// save validation errors
					$this->Roster->_validationErrors[$roster] = $this->Roster->validationErrors;					
				}
			}
			
			// find the signed up parent for this child. by default, it's this user. then it's household contact.
			$pValidates = true;
			// get signed up users
			$possibleParents = Set::extract('/Roster/Roster/user_id', $this->data);
			// get household contacts found that are signed up
			$contacts = array_intersect(Set::extract('/HouseholdMember/Household/contact_id'), $possibleParents);
			if (in_array($user['User']['id'], $possibleParents)) {
				$parent = $user['User']['id'];
			} elseif (count($contacts) > 0) {
				$parent = $contacts[0];
			} elseif (count($possibleParents) > 0) {
				$parent = $possibleParents[0];
			} else {
				$pValidates = false;
			}			
			
			// extract info to check/save for childcare
			$cValidates = true;
			if (isset($this->data['Child']) && $pValidates) {
				foreach ($this->data['Child'] as &$child) {
					$child['Roster']['roster_status_id'] = $confirmStatus['RosterStatus']['id'];
					$child['Roster']['parent_id'] = $parent;
					$child['Roster']['involvement_id'] = $this->data['Default']['involvement_id'];
										
					// only add a payment if we're taking one
					if ($involvement['Involvement']['take_payment'] && $this->data['Default']['payment_option_id'] > 0 && isset($this->data['CreditCard'])) {
						$amount = $paymentOption['PaymentOption']['childcare'];
						
						// add payment record to be saved (transaction id to be added later)
						$child['Payment'] = array(
							'0' => array(
								'user_id' => $child['Roster']['user_id'],
								'amount' => $amount,
								'payment_type_id' => $paymentType['PaymentType']['id'],
								'number' => substr($this->data['CreditCard']['credit_card_number'], -4),
								'payment_placed_by' => $this->activeUser['User']['id'],
								'payment_option_id' => $this->data['Default']['payment_option_id'],
								'comment' => $this->data['CreditCard']['first_name'].' '.$this->data['CreditCard']['last_name'].'\'s card processed by '.$this->activeUser['Profile']['name'].'.'
							)
						);
					}

					// save validate success only if we haven't failed yet (so not to overwrite a failure)
					if ($cValidates) {
						$cValidates = $this->Roster->saveAll($child, array('validate' => 'only'));
					} else {
						// still validate this roster to generate errors
						$this->Roster->saveAll($child, array('validate' => 'only'));
					}
				}			
			} else {
				$cValidates = true;
			}
			
			// combine roster validation errors
			$this->Roster->validationErrors = $this->Roster->_validationErrors;
			// check all validation before continuing with save
			if ($rValidates && $cValidates && $pValidates) {				
				// Now that we know that the data will save, let's run the credit card
				// get all signed up users (for their name)
				if ($involvement['Involvement']['take_payment'] && $this->data['Default']['payment_option_id'] > 0 && isset($this->data['CreditCard'])) {
					$signedupUsers = $this->Roster->User->Profile->find('all', array(
						'conditions' => array(
							'user_id' => array_merge(Set::extract('/Roster/Roster/user_id', $this->data), Set::extract('/Child/Roster/user_id', $this->data))
						),
						'contain' => false
					));
					$verb = count($signedupUsers) > 1 ? 'have' : 'has';
					$description = implode(' and ', Set::extract('/Profile/name', $signedupUsers)).' '.$verb.' been signed up for '.$involvement['InvolvementType']['name'].' '.$involvement['Involvement']['name'];
					// calculate amount	(use array_values to reset keys)
					$amount = Set::apply('/Payment/amount', array_values($this->data['Roster']), 'array_sum');
					if (isset($this->data['Child'])) {
						$amount += Set::apply('/Payment/amount', array_values($this->data['Child']), 'array_sum');
					}
										
					$this->data['CreditCard']['invoice_number'] = $paymentOption['PaymentOption']['account_code'];
					$this->data['CreditCard']['description'] = $description;
					$this->data['CreditCard']['email'] = $user['Profile']['primary_email'];			
					$this->data['CreditCard']['amount'] = $amount;				
					
					if ($this->Roster->CreditCard->save($this->data['CreditCard'])) {
						// save main rosters
						foreach ($this->data['Roster'] as $signuproster) {
							$this->Roster->create();
							// save transaction id
							$signuproster['Payment'][0]['transaction_id'] = $this->Roster->CreditCard->transactionId;
							$this->Roster->saveAll($signuproster, array('validate' => false));
							
							$this->set('involvement', $involvement);
							$this->Notifier->notify($signuproster['Roster']['user_id'], 'involvement_signup');
							$this->_sendEmail(array(
								'to' => $signuproster['Roster']['user_id'],
								'subject' => 'Signed up for '.$involvement['InvolvementType']['name'],
								'template' => 'involvements_add_signup'
							));
						}
					
						// save childcares
						if (isset($this->data['Child']) && count($this->data['Child'])) {
							foreach ($this->data['Child'] as $signupchild) {
								$this->Roster->create();
								// save transaction id
								$signupchild['Payment'][0]['transaction_id'] = $this->Roster->CreditCard->transactionId;
								$this->Roster->saveAll($signupchild, array('validate' => false));
								$this->set('involvement', $involvement);
								$this->Notifier->notify($signupchild['Roster']['user_id'], 'involvement_signup');
							}
						}
						
						$this->set('involvement', $involvement);
						$this->Notifier->notify($this->activeUser['User']['id'], 'payments_payment_made');
						$this->_sendEmail(array(
							'to' => $this->activeUser['User']['id'],
							'subject' => 'Payment made for '.$involvement['InvolvementType']['name'],
							'template' => 'payments_payment_made'
						));
						
						$this->Session->setFlash('You\'ve been signed up!', 'flash_success');
						$this->redirect(array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $involvementId));
					} else {
						$this->Roster->CreditCard->invalidate('credit_card_number', $this->Roster->CreditCard->creditCardError);
						$this->Session->setFlash('Error processing credit card.', 'flash_failure');
					}
				} else {
					// no credit card, just save as normal
					// save main rosters
					foreach ($this->data['Roster'] as $signuproster) {
						$this->Roster->create();
						$this->Roster->saveAll($signuproster, array('validate' => false));
						$this->set('involvement', $involvement);
						$this->Notifier->notify($signuproster['Roster']['user_id'], 'involvement_signup');
						$this->_sendEmail(array(
							'to' => $signuproster['Roster']['user_id'],
							'subject' => 'Signed up for '.$involvement['InvolvementType']['name'],
							'template' => 'involvements_add_signup'
						));
					}
					
					// save childcares
					if (isset($this->data['Child']) && count($this->data['Child'])) {
						foreach ($this->data['Child'] as $signupchild) {
							$this->Roster->create();
							$this->Roster->saveAll($signupchild, array('validate' => false));
							$this->set('involvement', $involvement);
							$this->Notifier->notify($signupchild['Roster']['user_id'], 'involvement_signup');
						}
					}
					
					$this->Session->setFlash('You\'ve been signed up!', 'flash_success');
					$this->redirect(array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $involvementId));
				}		
			} else {
				if (!$pValidates && isset($this->data['Child'])) {
					$this->Session->setFlash('Please select a parent to bring the children.', 'flash_failure');
				} else {
					// set validation error so modal doesn't close
					if (empty($this->Roster->validationErrors)) {
						$this->Roster->validationErrors = array('validation' => 'failed');
					}
					$this->Session->setFlash('You couldn\'t be signed up. Please, try again.', 'flash_failure');
				}
			}
		}
		
		// get user addresses for js
		$userAddresses = $this->Roster->User->Address->find('all', array(
			'conditions' => array(
				'foreign_key' => $userId,
				'model' => 'User'
			)
		));
		// format for select
		$addresses = Set::combine($userAddresses, '/Address/id', '/Address/name');
		
		// get involvement's payment options for js
		$involvementPaymentOptions = $this->Roster->PaymentOption->find('all', array(
			'conditions' => array(
				'involvement_id' => $involvementId
			)
		));
		// format for select
		$paymentOptions = Set::combine($involvementPaymentOptions, '/PaymentOption/id', '/PaymentOption/name');
		
		$this->set('roles', $this->Roster->Role->find('list', array(
			'conditions' => array(
				'ministry_id' => $involvement['Involvement']['ministry_id']
			)
		)));
		$this->set(compact('involvement', 'user', 'addresses', 'userAddresses', 'paymentOptions', 'involvementPaymentOptions'));
		$this->set('roster', $involvementRoster);
	}

/**
 * Edits a roster
 *
 * @param integer $id The id of the roster to edit
 * @todo Restrict to proper permissions
 */
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid roster', true));
			$this->redirect(array('action' => 'index'));
		}
		
		// get roster ids for comparison (to see if they're signed up)
		$thisRoster = $this->Roster->read(null, $id);
		
		// get roster ids for comparison (to see if they're signed up)
		$roster = $this->Roster->find('list', array(
			'conditions' => array(
				'Roster.id',
				'Roster.involvement_id' => $thisRoster['Roster']['involvement_id']
			),
			'fields' => array(
				'Roster.id',
				'Roster.user_id'
			),
			'contain' => false
		));
		
		if (!empty($this->data)) {
			// append status to defaults
			$confirmStatus = $this->Roster->RosterStatus->findByName('Confirmed');
			$this->data['Roster']['roster_status_id'] = $confirmStatus['RosterStatus']['id'];
			
			if (isset($this->data['Child'])) {
				foreach ($this->data['Child'] as &$child) {
					$child['roster_status_id'] = $confirmStatus['RosterStatus']['id'];
					$child['parent_id'] = $this->data['Roster']['user_id'];
					$child['involvement_id'] = $this->data['Roster']['involvement_id'];
					$child['payment_option_id'] = $this->data['Roster']['payment_option_id'];
				}
				
				$children = $this->data['Child'];
				unset($this->data['Child']);
				
				$cValidates = $this->Roster->saveAll($children, array('validate' => 'only'));
			} else {
				$cValidates = true;
			}
			
			$rValidates = $this->Roster->saveAll($this->data, array('validate' => 'only'));
			
			if ($rValidates && $cValidates) {
				$this->Roster->saveAll($this->data, array('validate' => false));
				
				if (isset($children)) {
					$this->Roster->saveAll($children, array('validate' => false));
				}
				
				$this->Session->setFlash('Your roster has been updated!', 'flash_success');
			} else {
				$this->Session->setFlash('There was an error with the changes.', 'flash_failure');
			}
			
			if (isset($children)) {
				$this->data['Child'] = $children;
			}
		}
		
		// get needed information about the user and this involvement
		$involvement = $this->Roster->Involvement->read(null, $thisRoster['Roster']['involvement_id']);
		// get user info and all household info where they are the contact
		$user = $this->Roster->User->find('first', array(
			'conditions' => array(
				'User.id' => $thisRoster['Roster']['user_id']
			),
			'contain' => array(
				'Profile',
				'HouseholdMember' => array(
					'Household' => array(
						'HouseholdMember' => array(
							'conditions' => array(
								'not' => array(
									'HouseholdMember.user_id' => $roster
								)
							),
							'User' => array(
								'Profile' => array(
									'conditions' => array(
										'or' => array(
											'Profile.household_contact_signups' => true,
											'Profile.child' => true
										)
									)
								)
							)
						)
					)				
				),
				'Group'
			)
		));
		
		if (empty($this->data)) {
			$this->data = $this->Roster->read(null, $id);
		}
		
		$paymentOptions = $this->Roster->PaymentOption->find('list', array(
			'conditions' => array(
				'involvement_id' => $involvement['Involvement']['id']
			)
		));
		
		$this->set(compact('involvement', 'user', 'roster', 'paymentOptions'));
	}

/**
 * Deletes a roster
 *
 * @param integer $id The id of the roster to delete
 * @todo Restrict to proper permissions
 */
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for roster', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Roster->recursive = -1;
		$roster = $this->Roster->read(null, $id);
		// delete any children too
		if ($this->Roster->deleteAll(array(
			'or' => array(
				'Roster.user_id' => $roster['Roster']['user_id'],
				'Roster.parent_id' => $roster['Roster']['user_id']
			),
			'Roster.involvement_id' => $roster['Roster']['involvement_id']
		))) {
			$this->set('involvement', $this->Roster->Involvement->read(null, $roster['Roster']['involvement_id']));
			$this->set('user', $this->Roster->Leader->User->read(null, $roster['Roster']['user_id']));
			// notify the user that they left
			$this->Notifier->notify($roster['Roster']['user_id'], 'rosters_delete');
			$this->_sendEmail(array(
				'to' => $roster['Roster']['user_id'],
				'subject' => 'Password reset',
				'template' => 'rosters_delete'
			));
			
			// notify all the leaders
			$leaders = $this->Roster->Leader->find('all', array(
				'model_id' => $roster['Roster']['involvement_id'],
				'model' => 'Involvement'
			));
			foreach ($leaders as $leader) {
				$this->set('user', $this->Roster->Leader->User->read(null, $leader['User']['id']));
				$this->Notifier->notify($leader['User']['id'], 'rosters_delete');
				$this->_sendEmail(array(
					'to' => $leader['User']['id'],
					'subject' => 'Password reset',
					'template' => 'rosters_delete'
				));
			}
			
			$this->Session->setFlash(__('Roster deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Roster was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>