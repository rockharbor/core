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
	var $components = array('FilterPagination', 'AuthorizeDotNet', 'MultiSelect.MultiSelect');

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array('Formatting', 'MultiSelect.MultiSelect');
	
/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		$this->Security->blackHoleCallback = '_forceSSL';
		$this->Security->requireSecure('add');
		parent::beforeFilter();
	}
	
/**
 * Displays a roster list
 *
 * ### Passed args:
 * - integer $Involvement The id of the involvement to filter for
 * - integer $User The id of the user to filter for
 *
 * @todo place user list limit into involvement()
 */ 
	function index() {
		$conditions = array();
		$userConditions = array();
		$involvementId = $this->passedArgs['Involvement'];
		
		// if involvement is defined, show just that involvement
		$conditions['Roster.involvement_id'] = $involvementId;
		
		// get roster ids
		$roster = $this->Roster->find('all', compact('conditions'));
		$rosterIds = Set::extract('/Roster/user_id', $roster);

		// if we're limiting this to one user, just pull their household signup data
		$householdIds = array();
		if (isset($this->passedArgs['User'])) {
			$householdIds = $this->Roster->User->HouseholdMember->Household->getMemberIds($this->passedArgs['User'], true);
			$viewableIds = array_intersect($householdIds, $rosterIds);
			$viewableIds[] = $this->passedArgs['User'];
			$conditions['User.id'] = $viewableIds;
		}

		if (!empty($this->data)) {
			if ($this->data['Filter']['Roster']['pending'] == 1) {
				$conditions['Roster.roster_status'] = 0;
			}
			$conditions += $this->Roster->parseCriteria(array('roles' => $this->data['Filter']['Role']));
		}
		
		$link = array(
			'User' => array(
				'Profile' => array(
					'fields' => array(
						'name',
						'cell_phone',
						'allow_sponsorage'
					)
				),
				'Image'
			),
		);
		$contain = array('Role');
		
		$this->Roster->recursive = 0;
		$this->paginate = compact('conditions','link','contain');
		
		// save search for multi select actions
		$this->MultiSelect->saveSearch($this->paginate);
		
		// set based on criteria
		$this->set('canCheckAll', !isset($this->passedArgs['User']));
		$this->set('rosters', $this->paginate());	
		$this->Roster->Involvement->contain(array('InvolvementType', 'Leader'));
		$involvement = $this->Roster->Involvement->read(null, $involvementId);
		$statuses = $this->Roster->statuses;
		
		$this->set(compact('involvement', 'rosterIds', 'householdIds', 'statuses'));
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
			$db = $this->Roster->getDataSource();
			$conditions[] = $db->expression('('.$this->Roster->Involvement->getVirtualField('passed').') = 0');
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
		$userId = $this->passedArgs['User'];
		$involvementId = $this->passedArgs['Involvement'];
		
		if (!$userId || !$involvementId) {
			$this->Session->setFlash('Invalid id', 'flash'.DS.'failure');
			$this->redirect(array('action'=>'index'));
		}

		// get needed information about the user and this involvement
		$this->Roster->Involvement->contain(array('InvolvementType'));
		$involvement = $this->Roster->Involvement->read(null, $involvementId);

		// can't sign up for inactive involvements
		if (!$involvement['Involvement']['active']) {
			$this->Session->setFlash('You cannot sign up for an inactive event.', 'flash'.DS.'failure');
			$this->redirect($this->emptyPage);
		}
		
		// create model to make use of validation
		$CreditCard = ClassRegistry::init('CreditCard');
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
		
		$this->Roster->User->contain(array('Profile'));
		$user = $this->Roster->User->read(null, $userId);

		$members = $this->Roster->User->HouseholdMember->Household->getMemberIds($userId, true);
				
		// they're submitting the form
		if (!empty($this->data)) {
			// first thing we'll do is validate all the data. if it all validates, we'll try to
			// process the credit card. if the credit card goes through, we'll add everyone to the 
			// roster (including childcare) and save the payment info
						
			// get chosen payment option
			$paymentOption = array();
			$paymentType = array();
			if ($involvement['Involvement']['take_payment']) {
				$paymentOption = $this->Roster->PaymentOption->read(null, $this->data['Default']['payment_option_id']);
				$paymentType = $this->Roster->Payment->PaymentType->findByName('Credit Card');
			}
			
			// extract info to check/save for roster
			$rValidates = true;
			$this->Roster->_validationErrors = array();
			
			foreach ($this->data['Roster'] as $roster => &$values) {
				$values = $this->Roster->setDefaultData(array(
					'roster' => $values,
					'involvement' => $involvement,
					'defaults' => $this->data['Default'],
					'creditCard' => $this->data,
					'payer' => $this->activeUser,
					'paymentOption' => $paymentOption,
					'paymentType' => $paymentType
				));

				// save validate success only if we haven't failed yet (so not to overwrite a failure)
				if ($rValidates) {
					$rValidates = $this->Roster->saveAll($values, array('validate' => 'only'));
				} else {
					// still validate this roster to generate errors
					$this->Roster->saveAll($values, array('validate' => 'only'));
				}

				// save validation errors
				if (!empty($this->Roster->validationErrors)) {
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
				foreach ($this->data['Child'] as $roster => &$child) {
					$child = $this->Roster->setDefaultData(array(
						'roster' => $child,
						'involvement' => $involvement,
						'defaults' => $this->data['Default'],
						'creditCard' => $this->data,
						'payer' => $this->activeUser,
						'paymentOption' => $paymentOption,
						'paymentType' => $paymentType,
						'parent' => $parent
					));

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

			// check to make sure this doesn't exceed the roster limit
			$lValidates = true;
			$currentCount = $this->Roster->find('count', array(
				'conditions' => array(
					'Roster.involvement_id' => $involvement['Involvement']['id']
				),
				'contain' => false
			));
			$rosterCount = count($this->data['Roster']);
			$childCount = isset($this->data['Child']) ? count($this->data['Child']) : 0;
			if (!empty($involvement['Involvement']['roster_limit'])) {
				$lValidates = $rosterCount + $childCount + $currentCount <= $involvement['Involvement']['roster_limit'];
			} else {
				$lValidates = true;
			}

			$this->set('involvement', $involvement);

			// combine roster validation errors
			$this->Roster->validationErrors = $this->Roster->_validationErrors;
			// check all validation before continuing with save
			if ($lValidates && $rValidates && $cValidates && $pValidates) {
				// Now that we know that the data will save, let's run the credit card
				// get all signed up users (for their name)
				if ($involvement['Involvement']['take_payment'] && $this->data['Default']['payment_option_id'] > 0 && !$this->data['Default']['pay_later']) {
					$signedUpIds = array_merge(Set::extract('/Roster/Roster/user_id', $this->data), Set::extract('/Child/Roster/user_id', $this->data));
					$signedupUsers = $this->Roster->User->Profile->find('all', array(
						'conditions' => array(
							'user_id' => $signedUpIds
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
					
					if ($CreditCard->save($this->data['CreditCard'])) {
						// save main rosters
						foreach ($this->data['Roster'] as $signuproster) {
							$this->Roster->create();
							// include transaction id
							$signuproster['Payment'][0]['transaction_id'] = $CreditCard->transactionId;
							$this->Roster->saveAll($signuproster, array('validate' => false));							
							$this->Notifier->notify(array(
								'to' => $signuproster['Roster']['user_id'],
								'template' => 'involvements_signup',
								'subject' => 'Signed up for '.$involvement['InvolvementType']['name'],
							));
						}
					
						// save childcares
						if (isset($this->data['Child']) && count($this->data['Child'])) {
							foreach ($this->data['Child'] as $signupchild) {
								$this->Roster->create();
								// include transaction id
								$signupchild['Payment'][0]['transaction_id'] = $CreditCard->transactionId;
								$this->Roster->saveAll($signupchild, array('validate' => false));
								$this->Notifier->notify(array(
								'to' => $signupchild['Roster']['user_id'],
								'template' => 'involvements_signup',
								), 'notification');
							}
						}
						
						$this->Notifier->notify(array(
							'to' => $this->activeUser['User']['id'],
							'template' => 'payments_payment_made',
							'subject' => 'Payment made for '.$involvement['InvolvementType']['name'],
						));
						$this->Session->setFlash('You\'ve been signed up!', 'flash'.DS.'success');
						$this->redirect(array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $involvementId));
					} else {
						$CreditCard->invalidate('credit_card_number', $CreditCard->creditCardError);
						$this->Session->setFlash('Error processing credit card.', 'flash'.DS.'failure');
					}
				} else {
					// no credit card, just save as normal
					// save main rosters
					foreach ($this->data['Roster'] as $signuproster) {
						$this->Roster->create();
						$this->Roster->saveAll($signuproster, array('validate' => false));
						$this->Notifier->notify(array(
							'to' => $signuproster['Roster']['user_id'],
							'template' => 'involvements_signup',
							'subject' => 'Signed up for '.$involvement['InvolvementType']['name'],
						));
					}
					
					// save childcares
					if (isset($this->data['Child']) && count($this->data['Child'])) {
						foreach ($this->data['Child'] as $signupchild) {
							$this->Roster->create();
							$this->Roster->saveAll($signupchild, array('validate' => false));
							$this->Notifier->notify(array(
								'to' => $signupchild['Roster']['user_id'],
								'template' => 'involvements_signup',
							), 'notification');
						}
					}
					
					$this->Session->setFlash('You\'ve been signed up!', 'flash'.DS.'success');
					$this->redirect(array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $involvementId));
				}		
			} else {
				// set validation error so modal doesn't close
				if (empty($this->Roster->validationErrors)) {
					$this->Roster->validationErrors = array('validation' => 'failed');
				}

				if (!$pValidates && isset($this->data['Child'])) {
					$this->Session->setFlash('Please select a parent to bring the children.', 'flash'.DS.'failure');
				} elseif (!$lValidates) {
					$this->Session->setFlash('The roster limit has been reached. Please sign up less people or wait for room to become available.', 'flash'.DS.'failure');
				} else {
					$this->Session->setFlash('You couldn\'t be signed up. Please, try again.', 'flash'.DS.'failure');
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
			$this->data['Roster']['roster_status'] = 1;
			
			if (isset($this->data['Child'])) {
				foreach ($this->data['Child'] as &$child) {
					$child['roster_status'] = 1;
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
				
				$this->Session->setFlash('Your roster has been updated!', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('There was an error with the changes.', 'flash'.DS.'failure');
			}
			
			if (isset($children)) {
				$this->data['Child'] = $children;
			}
		}
		
		// get needed information about the user and this involvement
		$involvement = $this->Roster->Involvement->read(null, $thisRoster['Roster']['involvement_id']);
		// get user info and all household info where they are the contact
		$householdMemberIds = $this->Roster->User->HouseholdMember->Household->getMemberIds($thisRoster['Roster']['user_id'], true);
		$householdMembers = $this->Roster->User->find('all', array(
			'conditions' => array(
				'User.id' => $householdMemberIds
			),
			'contain' => array(
				'Profile',
				'Group'
			)
		));
		$this->Roster->User->contain(array(
			'Profile',
			'Group'
		));
		$user = $this->Roster->User->read(null, $thisRoster['Roster']['user_id']);
		
		if (empty($this->data)) {
			$this->data = $this->Roster->read(null, $id);
		}
		
		$paymentOptions = $this->Roster->PaymentOption->find('list', array(
			'conditions' => array(
				'involvement_id' => $involvement['Involvement']['id']
			)
		));
		
		$this->set(compact('involvement', 'user', 'roster', 'paymentOptions', 'householdMembers'));
	}

/**
 * Saves roles to a roster id OR
 * Saves a new role to the ministry
 *
 * ### Passed Args:
 * - `Involvement` the involvement id
 */
	function roles($roster_id) {
		if (!empty($this->data)) {
			if (isset($this->data['Role']['ministry_id'])) {
				$this->Roster->Role->save($this->data);
			} else {
				$this->Roster->saveAll($this->data);
				$this->Roster->clearCache();
			}			
		}
		$this->Roster->contain(array(
			'Role'
		));
		$involvement = $this->Roster->Involvement->read('ministry_id', $this->passedArgs['Involvement']);
		if (empty($this->data) || isset($this->data['Role']['ministry_id'])) {
			$this->data = $this->Roster->read(null, $roster_id);
		}
		$roles = $this->Roster->Role->find('list', array(
			'conditions' => array(
				'Role.ministry_id' => $involvement['Involvement']['ministry_id']
			)
		));
		$ministry_id = $involvement['Involvement']['ministry_id'];
		$this->set(compact('roles', 'ministry_id'));
	}

/**
 * Confirms a set of roster ids
 *
 * @param integer $uid The multi select id
 */
	function confirm($uid = null) {
		$selected = $this->MultiSelect->getSelected($uid);
		$this->Roster->updateAll(
			array('Roster.roster_status' => 1),
			array('Roster.id' => $selected)
		);
		$this->Session->setFlash(__('Roster confirmed', true));
		$this->redirect(array('action'=>'index'));
	}

/**
 * Deletes a set of roster ids
 *
 * @param integer $uid The multi select id
 * @todo Restrict to proper permissions
 */
	function delete($uid = null) {
		$selected = $this->MultiSelect->getSelected($uid);

		if (empty($selected)) {
			$this->Session->setFlash(__('Roster was not deleted', true));
			$this->redirect(array('action' => 'index'));
		}
		foreach ($selected as $rosterId) {
			$this->Roster->recursive = -1;
			$roster = $this->Roster->read(null, $rosterId);
			// delete any children too
			if ($this->Roster->deleteAll(array(
				'or' => array(
					'Roster.user_id' => $roster['Roster']['user_id'],
					'Roster.parent_id' => $roster['Roster']['user_id']
				),
				'Roster.involvement_id' => $roster['Roster']['involvement_id']
			))) {
				$this->Roster->Involvement->contain(array('InvolvementType'));
				$this->Roster->Involvement->Leader->User->contain(array('Profile'));
				$this->set('involvement', $this->Roster->Involvement->read(null, $roster['Roster']['involvement_id']));
				$this->set('user', $this->Roster->Involvement->Leader->User->read(null, $roster['Roster']['user_id']));
				$this->set('activeUser', $this->activeUser);
				// notify the user that they left
				$this->Notifier->notify(array(
					'to' => $roster['Roster']['user_id'],
					'template' => 'rosters_delete',
					'subject' => 'Left involvement',
				));
			}
		}
		// notify all the leaders
		$leaders = $this->Roster->Involvement->Leader->find('all', array(
			'conditions' => array(
				'model_id' => $roster['Roster']['involvement_id'],
				'model' => 'Involvement'
			)
		));
		foreach ($leaders as $leader) {
			$this->set('user', $this->Roster->Involvement->Leader->User->read(null, $leader['Leader']['user_id']));
			$this->Notifier->notify(array(
				'to' => $leader['Leader']['user_id'],
				'template' => 'rosters_delete',
				'subject' => 'Left involvement',
			));
		}
		$this->Session->setFlash(__('Roster deleted', true));
		$this->redirect(array('action'=>'index'));
	}
}
?>