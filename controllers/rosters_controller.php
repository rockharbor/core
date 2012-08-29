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
	var $components = array(
		'FilterPagination' => array(
			'startEmpty' => false
		),
		'AuthorizeDotNet',
		'MultiSelect.MultiSelect'
	);

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array(
		'Formatting', 
		'MultiSelect.MultiSelect'
	);
	
/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		// index is special, in that its limitations are checked within the action
		// there is also an ACL for it for checking if admin should have permission
		$this->Auth->allow('index');
		
		$this->_editSelf('status', 'delete');
		parent::beforeFilter();
		$this->_editSelf('involvement', 'add');
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
		
		$involvement = $this->Roster->Involvement->find('first', array(
			'fields' => array(
				'roster_visible',
				'ministry_id',
				'take_payment'
			),
			'conditions' => array(
				'Involvement.id' => $involvementId
			),
			'contain' => array(
				'Ministry' => array(
					'fields' => array(
						'campus_id'
					)
				)
			)
		));
		$roster = $this->Roster->find('first', array(
			'fields' => array(
				'roster_status_id'
			),
			'conditions' => array(
				'user_id' => $this->activeUser['User']['id'],
				'involvement_id' => $involvementId
			),
			'contain' => false
		));
		$inRoster = !empty($roster);
		
		$fullAccess = 
			$this->Roster->Involvement->isLeader($this->activeUser['User']['id'], $involvementId)
			|| $this->Roster->Involvement->Ministry->isManager($this->activeUser['User']['id'], $involvement['Involvement']['ministry_id'])
			|| $this->Roster->Involvement->Ministry->Campus->isManager($this->activeUser['User']['id'], $involvement['Ministry']['campus_id'])
			|| $this->isAuthorized('rosters/index', array('Involvement' => $id));
		
		$canSeeRoster = 
			($inRoster && $roster['Roster']['roster_status_id'] == 1 && $involvement['Involvement']['roster_visible'])
			|| $fullAccess;
		
		if (!$canSeeRoster) {
			$this->cakeError('privateItem', array('type' => 'Roster'));
		}
		
		// if involvement is defined, show just that involvement
		$conditions['Roster.involvement_id'] = $involvementId;
		
		if (!empty($this->data)) {
			if (!empty($this->data['Filter']['roster_status_id'])) {
				$conditions['Roster.roster_status_id'] = $this->data['Filter']['roster_status_id'];
			}
			$conditions += $this->Roster->parseCriteria(array('roles' => $this->data['Filter']['Role']));
		} else {
			$this->data = array(
				'Filter' => array(
					'pending' => 0,
					'Role' => array()
				)
			);
		}
		
		$link = array(
			'User' => array(
				'fields' => array(
					'id', 
					'username',
					'active',
					'flagged'
				),
				'Profile' => array(
					'fields' => array(
						'name',
						'cell_phone',
						'allow_sponsorage',
						'primary_email',
						'background_check_complete'
					)
				)
			),
			'RosterStatus' => array(
				'fields' => array(
					'name'
				)
			)
		);
		$contain = array('Role');

		$this->Roster->recursive = -1;
		$fields = array('id', 'created', 'user_id');
		if ($involvement['Involvement']['take_payment']) {
			array_push($fields, 'amount_due', 'amount_paid', 'balance');
		}
		$this->paginate = compact('conditions','link','contain','fields');
		
		// save search for multi select actions
		$this->MultiSelect->saveSearch($this->paginate);
		
		// set based on criteria
		$this->Roster->Involvement->contain(array('InvolvementType', 'Leader'));
		$involvement = $this->Roster->Involvement->read(null, $involvementId);
		
		$rosters =  $this->FilterPagination->paginate();
		$counts['childcare'] = $this->Roster->find('count', array(
			'conditions' => array_merge_recursive($conditions, array('Roster.parent_id >' => 0))
		));
		$counts['pending'] = $this->Roster->find('count', array(
			'conditions' => array_merge_recursive($conditions, array('Roster.roster_status_id' => 2))
		));
		$counts['leaders'] = count($involvement['Leader']);
		$counts['confirmed'] = $this->Roster->find('count', array(
			'conditions' => array_merge_recursive($conditions, array('Roster.roster_status_id' => 1))
		));
		$counts['total'] = $this->params['paging']['Roster']['count'];
		
		$roles = $this->Roster->Involvement->Ministry->Role->find('list', array(
			'conditions' => array(
				'Role.id' => $this->Roster->Involvement->Ministry->Role->findRoles($involvement['Involvement']['ministry_id'])
			)
		));
		$rosterStatuses = $this->Roster->RosterStatus->find('list');

		$this->set(compact('rosters', 'involvement', 'rosterIds', 'rosterStatuses', 'counts', 'roles', 'fullAccess'));
	}

/**
 * Shows involvement history
 *
 * ### Passed args:
 * - integer $User The id of the user
 */ 	
	function involvement() {
		$userId = $this->passedArgs['User'];
		
		if (!$userId) {
			$this->cakeError('error404');
		}

		$leaderOf = $this->Roster->Involvement->Leader->find('list', array(
			'fields' => array(
				'Leader.id',
				'Leader.model_id'				
			),
			'conditions' => array(
				'Leader.model' => 'Involvement',
				'Leader.user_id' => $userId
			)
		));

		$memberOf = $this->Roster->find('list', array(
			'fields' => array(
				'Roster.id',
				'Roster.involvement_id'
			),
			'conditions' => array(
				'Roster.user_id' => $userId
			)
		));

		$conditions = array(
			'Involvement.id' => array_values($memberOf)	
		);

		if ($this->Session->check('FilterPagination.data') && empty($this->data)) {
			$this->data = $this->Session->read('FilterPagination.data');
		}
		$_default = array(
			'Roster' => array(
				'previous' => 0,
				'leading' => 1,
				'inactive' => 0,
				'private' => 1
			)
		);
		$this->data = $search = Set::merge($_default, $this->data);
		
		if ($this->data['Roster']['leading']) {
			$conditions['Involvement.id'] = array_merge(array_values($leaderOf), array_values($memberOf));
		}
		if ($this->data['Roster']['previous'] == false) {
			$db = $this->Roster->getDataSource();
			$conditions[] = $db->expression('('.$this->Roster->Involvement->getVirtualField('previous').') = '.$this->data['Roster']['previous']);
		}
		if (!$this->data['Roster']['inactive']) {
			$conditions['Involvement.active'] = true;
		}
		if (!$this->data['Roster']['private']) {
			$conditions['Involvement.private'] = false;
		}

		$this->paginate = array(
			'fields' => array(
				'id', 'name', 'previous', 'active', 'private'
			),
			'conditions' => $conditions,
			'contain' => array(					
				'Date',
				'InvolvementType',
				'Roster' => array(
					'conditions' => array(
						'Roster.user_id' => $userId
					),
					'Role'
				)
			)
		);
		$rosters = $this->FilterPagination->paginate('Involvement');
		foreach ($rosters as &$roster) {
			$roster['Involvement']['dates'] = $this->Roster->Involvement->Date->generateDates($roster['Involvement']['id'], array('limit' => 1));
		}

		$this->set(compact('userId', 'leaderOf', 'rosters', 'private', 'memberOf'));
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
			$this->cakeError('error404');
		}

		// get needed information about the user and this involvement
		$this->Roster->Involvement->contain(array('InvolvementType', 'Question'));
		$involvement = $this->Roster->Involvement->read(null, $involvementId);

		// can't sign up for inactive or past involvements
		if (!$involvement['Involvement']['active'] && !$involvement['Involvement']['previous']) {
			$this->Session->setFlash('Cannot sign up for an inactive or past event.', 'flash'.DS.'failure');
			$this->redirect($this->emptyPage);
		}
		
		// create model to make use of validation
		$CreditCard = ClassRegistry::init('CreditCard');
		///HouseholdMember/Household/HouseholdMember/User/Profile
		$this->Roster->User->contain(array(
			'Profile',
			'HouseholdMember' => array(
				'Household' => array(
					'HouseholdMember' => array(
						'User' => array(
							'Profile'
						)
					)
				)
			)
		));
		$user = $this->Roster->User->read(null, $userId);

		// they're submitting the form
		if (!empty($this->data)) {
			// first thing we'll do is validate all the data. if it all validates, we'll try to
			// process the credit card. if the credit card goes through, we'll add everyone to the 
			// roster (including childcare) and save the payment info
						
			// extract info to check/save for roster
			$rValidates = true;
			$this->Roster->_validationErrors = array();
			
			foreach ($this->data['Adult'] as $roster => &$values) {
				if ($values['Roster']['user_id'] == 0) {
					unset($this->data['Adult'][$roster]);
					continue;
				}
				$values = $this->Roster->setDefaultData(array(
					'roster' => $values,
					'involvement' => $involvement,
					'defaults' => $this->data['Default'],
					'creditCard' => $this->data,
					'payer' => $this->activeUser
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
			
			// extract info to check/save for childcare
			$pValidates = true;
			$cValidates = true;
			if (isset($this->data['Child'])) {
				$possibleParents = Set::extract('/Adult/Roster/user_id', $this->data);
				
				foreach ($this->data['Child'] as $roster => &$child) {
					if ($child['Roster']['user_id'] == 0) {
						unset($this->data['Child'][$roster]);
						continue;
					}
					
					// try to find the parent
					$parent = null;
					foreach ($possibleParents as $possibleParent) {
						if ($this->Roster->User->HouseholdMember->Household->isContactFor($possibleParent, $child['Roster']['user_id'])) {
							$parent = $possibleParent;
							break;
						}
					}
					
					if (empty($parent)) {
						$cValidates = $pValidates = false;
					}

					$child = $this->Roster->setDefaultData(array(
						'roster' => $child,
						'involvement' => $involvement,
						'defaults' => $this->data['Default'],
						'creditCard' => $this->data,
						'payer' => $this->activeUser,
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
			}

			// check to make sure this doesn't exceed the roster limit
			$lValidates = true;
			$currentCount = $this->Roster->find('count', array(
				'conditions' => array(
					'Roster.involvement_id' => $involvement['Involvement']['id'],
					'Roster.roster_status_id' => 1
				),
				'contain' => false
			));
			$rosterCount = count($this->data['Adult']);
			$childCount = isset($this->data['Child']) ? count($this->data['Child']) : 0;
			if (!empty($involvement['Involvement']['roster_limit'])) {
				$lValidates = $rosterCount + $childCount + $currentCount <= $involvement['Involvement']['roster_limit'];
			} else {
				$lValidates = true;
			}

			$this->set('involvement', $involvement);

			// combine roster validation errors
			$this->Roster->validationErrors = $this->Roster->_validationErrors;
			$Adult = new Model(array(
				'table' => false,
				'name' => 'Adult'
			));
			$Adult->validationErrors = $this->Roster->_validationErrors;
			// check all validation before continuing with save
			if ($rosterCount > 0 && $lValidates && $rValidates && $cValidates && $pValidates) {
				// Now that we know that the data will save, let's run the credit card
				// get all signed up users (for their name)
				
				// calculate amount	(use array_values to reset keys)
				$amount = Set::apply('/Payment/amount', array_values($this->data['Adult']), 'array_sum');
				if (isset($this->data['Child'])) {
					$amount += Set::apply('/Payment/amount', array_values($this->data['Child']), 'array_sum');
				}
				
				$signedUpIds = array_merge(Set::extract('/Adult/Roster/user_id', $this->data), Set::extract('/Child/Roster/user_id', $this->data));
				$signedupUsers = $this->Roster->User->Profile->find('all', array(
					'conditions' => array(
						'user_id' => $signedUpIds
					),
					'contain' => false
				));
				$verb = count($signedupUsers) > 1 ? 'have' : 'has';
				
				foreach ($signedupUsers as &$signedupUser) {
					$signedupUser['answers'] = Set::extract('/Adult/Roster[user_id='.$signedupUser['Profile']['user_id'].']/../Answer', $this->data);
				}
				
				$this->set(compact('verb', 'signedupUsers'));
				
				if ($involvement['Involvement']['take_payment'] && $this->data['Default']['payment_option_id'] > 0 && !$this->data['Default']['pay_later'] && $amount > 0) {
					$description = implode(' and ', Set::extract('/Profile/name', $signedupUsers)).' '.$verb.' been signed up for '.$involvement['InvolvementType']['name'].' '.$involvement['Involvement']['name'];
					
					$paymentOption = $this->Roster->PaymentOption->read(null, $this->data['Default']['payment_option_id']);
					$this->data['CreditCard']['invoice_number'] = $paymentOption['PaymentOption']['account_code'];
					$this->data['CreditCard']['description'] = $description;
					$this->data['CreditCard']['email'] = $user['Profile']['primary_email'];			
					$this->data['CreditCard']['amount'] = $amount;
					
					if ($CreditCard->save($this->data['CreditCard'])) {
						// save main rosters
						foreach ($this->data['Adult'] as $signuproster) {
							$this->Roster->create();
							// include transaction id
							$signuproster['Payment'][0]['transaction_id'] = $CreditCard->transactionId;
							$signuproster['Payment'][0]['number'] = substr($this->data['CreditCard']['credit_card_number'], -4);
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
								$signupchild['Payment'][0]['number'] = substr($this->data['CreditCard']['credit_card_number'], -4);
								$this->Roster->saveAll($signupchild, array('validate' => false));
								$this->Notifier->notify(array(
									'to' => $signupchild['Roster']['user_id'],
									'template' => 'involvements_signup',
									'subject' => 'Signed up for '.$involvement['InvolvementType']['name']
								));
							}
						}
						
						$leaders = $this->Roster->Involvement->getLeaders($involvement['Involvement']['id']);
						$this->set('amount', $amount);
						
						foreach ($leaders as $leader) {
							$this->Notifier->notify(array(
								'to' => $leader,
								'template' => 'involvements_signup_leader',
								'subject' => 'New user(s) signed up and paid for '.$involvement['Involvement']['name']
							));
							
							if ($rosterCount + $childCount + $currentCount == $involvement['Involvement']['roster_limit']) {
								$this->Notifier->notify(array(
									'to' => $leader,
									'template' => 'rosters_filled',
									'subject' => $involvement['Involvement']['name'].' roster filled'
								));
							}
						}
						
						$this->Notifier->notify(array(
							'to' => $this->activeUser['User']['id'],
							'template' => 'payments_payment_made',
							'subject' => 'Your payment has been made for '.$involvement['InvolvementType']['name'],
						));
						$this->Session->setFlash('Your payment has been received and you have signed up for '.$involvement['Involvement']['name'].'.', 'flash'.DS.'success');
						$this->redirect(array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $involvementId));
					} else {
						$this->validationErrors['CreditCard'] = $CreditCard->validationErrors;
						$this->Session->setFlash('Unable to process payment.', 'flash'.DS.'failure');
					}
				} else {
					// no credit card, just save as normal
					// save main rosters
					foreach ($this->data['Adult'] as $signuproster) {
						$this->Roster->create();
						$this->Roster->saveAll($signuproster, array('validate' => false));
						$this->Notifier->notify(array(
							'to' => $signuproster['Roster']['user_id'],
							'template' => 'involvements_signup',
							'subject' => 'You have signed up for '.$involvement['InvolvementType']['name'],
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
								'subject' => 'You have signed up for '.$involvement['InvolvementType']['name'],
							));
						}
					}
					
					$leaders = $this->Roster->Involvement->getLeaders($involvement['Involvement']['id']);
					foreach ($leaders as $leader) {
						$this->Notifier->notify(array(
							'to' => $leader,
							'template' => 'involvements_signup_leader',
							'subject' => 'New user(s) signed up for '.$involvement['Involvement']['name']
						));
						
						if ($rosterCount + $childCount + $currentCount == $involvement['Involvement']['roster_limit']) {
							$this->Notifier->notify(array(
								'to' => $leader,
								'template' => 'rosters_filled',
								'subject' => $involvement['Involvement']['name'].' roster filled'
							));
						}
					}
					
					$this->Session->setFlash('You have signed up for '.$involvement['Involvement']['name'].'.', 'flash'.DS.'success');
					$this->redirect(array('controller' => 'involvements', 'action' => 'view', 'Involvement' => $involvementId));
				}		
			} else {
				if (!$pValidates && isset($this->data['Child'])) {
					$msg = 'Please assign a parent to this child.';
				} elseif (!$lValidates) {
					$msg = 'Cannot join '.$involvement['Involvement']['name'].'. The roster is full.';
				} elseif ($rosterCount == 0) {
					$msg = 'Please choose everyone who is attending.';
				} else {
					$msg = 'Cannot join '.$involvement['Involvement']['name'].'. Please try again.';
				}
				
				// set validation error so modal doesn't close
				if (empty($this->Roster->validationErrors)) {
					$this->Roster->validationErrors = array('validation' => $msg);
				}
				
				$this->Session->setFlash($msg, 'flash'.DS.'failure');
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
		$paymentTypes = $this->Roster->Payment->PaymentType->find('all');
		
		$this->set('roles', $this->Roster->Role->find('list', array(
			'conditions' => array(
				'Role.id' => $this->Roster->Involvement->Ministry->Role->findRoles($involvement['Involvement']['ministry_id'])
			)
		)));
		$this->set(compact('involvement', 'user', 'addresses', 'userAddresses', 'paymentOptions', 'involvementPaymentOptions', 'paymentTypes'));
	}

/**
 * Edits a roster
 *
 * @param integer $id The id of the roster to edit
 * @todo Restrict to proper permissions
 */
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->cakeError('error404');
		}
		
		// get roster ids for comparison (to see if they're signed up)
		$thisRoster = $this->Roster->read(null, $id);
		
		// get needed information about the user and this involvement
		$this->Roster->Involvement->contain(array(
			'Ministry',
			'Question',
			'Roster' => array(
				'fields' => array(
					'user_id'
				)
			)
		));
		$involvement = $this->Roster->Involvement->read(null, $thisRoster['Roster']['involvement_id']);
		
		if (!empty($this->data)) {
			if ($this->Roster->Involvement->isLeader($this->activeUser['User']['id'], $involvement['Involvement']['id'])
			|| $this->Roster->Involvement->Ministry->isManager($this->activeUser['User']['id'], $involvement['Ministry']['id'])
			|| $this->Roster->Involvement->Ministry->Campus->isManager($this->activeUser['User']['id'], $involvement['Ministry']['campus_id'])
			) {
				$this->Roster->Answer->validate = array();
			}
			
			if (isset($this->data['Child'])) {
				foreach ($this->data['Child'] as $key => &$child) {
					if ($child['user_id'] == 0) {
						unset($this->data['Child'][$key]);
						continue;
					}
					$child['roster_status_id'] = 1;
					$child['parent_id'] = $this->data['Roster']['user_id'];
					$child['involvement_id'] = $this->data['Roster']['involvement_id'];
					$child['payment_option_id'] = $this->data['Roster']['payment_option_id'];
				}
				
				$children = $this->data['Child'];
				unset($this->data['Child']);
				
				if (count($children) > 0) {
					$cValidates = $this->Roster->saveAll($children, array('validate' => 'only'));
				} else {
					$cValidates = true;
				}
			} else {
				$cValidates = true;
			}
			
			$rValidates = $this->Roster->saveAll($this->data, array('validate' => 'only'));
			
			if ($rValidates && $cValidates) {
				$this->Roster->saveAll($this->data, array('validate' => false));
				
				if (isset($children) && count($children) > 0) {
					$this->Roster->saveAll($children, array('validate' => false));
				}
				
				$this->Session->setFlash('This roster has been saved.', 'flash'.DS.'success');
			} else {
				$this->Session->setFlash('Unable to save this roster. Please try again.', 'flash'.DS.'failure');
			}
			
			if (isset($children)) {
				$this->data['Child'] = $children;
			}
		}
		
		// get user info and all household info where they are the contact
		$signedUp = Set::extract('/Roster/user_id', $involvement);
		$householdMemberIds = $this->Roster->User->HouseholdMember->Household->getMemberIds($thisRoster['Roster']['user_id'], true);
		$householdMembers = $this->Roster->User->find('all', array(
			'conditions' => array(
				'User.id' => $householdMemberIds,
				'not' => array(
					'User.id' => $signedUp
				)
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
			$this->Roster->contain(array('Answer'));
			$this->data = $this->Roster->read(null, $id);
		}
		
		$paymentOptions = $this->Roster->PaymentOption->find('list', array(
			'conditions' => array(
				'involvement_id' => $involvement['Involvement']['id']
			)
		));
		$rosterStatuses = $this->Roster->RosterStatus->find('list');
		
		$fullAccess = 
			$this->Roster->Involvement->isLeader($this->activeUser['User']['id'], $involvement['Involvement']['id'])
			|| $this->Roster->Involvement->Ministry->isManager($this->activeUser['User']['id'], $involvement['Involvement']['ministry_id'])
			|| $this->Roster->Involvement->Ministry->Campus->isManager($this->activeUser['User']['id'], $involvement['Ministry']['campus_id'])
			|| $this->isAuthorized('rosters/index', array('Involvement' => $involvement['Involvement']['id']));
		
		$this->set(compact('involvement', 'user', 'roster', 'paymentOptions', 'householdMembers', 'rosterStatuses', 'fullAccess'));
	}

/**
 * Saves roles to a roster id
 *
 * ### Passed Args:
 * - `Involvement` the involvement id
 */
	function roles($roster_id) {
		if (!empty($this->data)) {
			$this->Roster->saveAll($this->data);
			$this->Roster->clearCache();		
		}
		$this->Roster->contain(array(
			'Role'
		));
		$this->Roster->Involvement->contain(array('Ministry' => array('fields' => array('id', 'name'))));
		$ministry = $this->Roster->Involvement->read(null, $this->passedArgs['Involvement']);
		if (empty($this->data) || isset($this->data['Role']['ministry_id'])) {
			$this->data = $this->Roster->read(null, $roster_id);
		}
		$roles = $this->Roster->Role->find('list', array(
			'conditions' => array(
				'Role.id' => $this->Roster->Involvement->Ministry->Role->findRoles($ministry['Ministry']['id'])
			)
		));
		$this->set(compact('roles', 'ministry'));
	}

/**
 * Confirms a set of roster ids
 *
 * @param integer $uid The multi select id or a single roster record id
 * @param integer $status The RosterStatus id
 */
	function status($uid = null, $status = 1) {
		$selected = $this->MultiSelect->getSelected($uid);
		if (empty($selected)) {
			$selected = $uid;
		}
		
		$success = $this->Roster->updateAll(
			array('Roster.roster_status_id' => $status),
			array('Roster.id' => $selected)
		);
		$this->Session->setFlash('Roster members confirmed.', 'flash'.DS.'success');
		if (isset($this->params['requested']) &&  $this->params['requested']) {
			return $success;
		}
		$this->redirect(array('action'=>'index'));
	}

/**
 * Deletes a set of roster ids
 *
 * @param integer $uid The multi select id
 */
	function delete($uid = null) {
		$selected = $this->MultiSelect->getSelected($uid);

		if (empty($selected) && ($uid && isset($this->passedArgs['User']))) {
			$roster = $this->Roster->read(null, $uid);
			if ($this->passedArgs['User'] !== $roster['Roster']['user_id'] &&
			!$this->Roster->User->HouseholdMember->Household->isContactFor($this->passedArgs['User'], $roster['Roster']['user_id'])
			) {
				$this->cakeError('error404');
			}
			$selected = array($uid);
		} elseif (empty($selected)) {
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
				$involvement = $this->Roster->Involvement->read(null, $roster['Roster']['involvement_id']);
				$this->set('involvement', $involvement);
				$leaver = $this->Roster->Involvement->Leader->User->read(null, $roster['Roster']['user_id']);
				$this->set('leaver', $leaver);
				$this->set('activeUser', $this->activeUser);
				// notify the user that they left
				$this->Notifier->notify(array(
					'to' => $roster['Roster']['user_id'],
					'template' => 'rosters_delete',
					'subject' => 'You have been removed from '.$involvement['Involvement']['name']
				));
			
				// notify all the leaders
				$leaders = $this->Roster->Involvement->getLeaders($roster['Roster']['involvement_id']);
				foreach ($leaders as $leader) {
					$this->Notifier->notify(array(
						'to' => $leader,
						'template' => 'rosters_delete_leader',
						'subject' => $leaver['Profile']['name'].' has been removed from '.$involvement['Involvement']['name']
					));
				}
			}
		}
		$this->Session->setFlash('Roster members removed.', 'flash'.DS.'success');
		$this->redirect(array('action'=>'index'));
	}
}
