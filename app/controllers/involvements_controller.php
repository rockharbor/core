<?php
/**
 * Involvement controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Involvements Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class InvolvementsController extends AppController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Involvements';

/**
 * Extra helpers for this controller
 *
 * @var array
 */
	var $helpers = array('Formatting');

/**
 * Extra components for this controller
 *
 * @var array
 */
	var $components = array('FilterPagination');
	
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
 * Shows a list of involvement opportunities
 */	
	function index() {
		$this->paginate = array(
			'contain' => array(
				'InvolvementType',
				'Date',
				'Ministry'
			)
		);
		
		$this->set('involvements', $this->paginate());
	}
	
/**
 * Views an involvement opportunity
 */
	function view() {
		$id = $this->passedArgs['Involvement'];
		
		if (!$id) {
			$this->Session->setFlash('Invalid involvement');
			$this->redirect(array('action' => 'index'));
		}
		$this->Involvement->contain(array(
			'Roster' => array(
				'User' => array(
					'Profile'
				)
			),
			'InvolvementType',
			'Date'
		));
		$this->set('involvement', $this->Involvement->read(null, $id));
	}

/**
 * Invites a roster to an involvement opportunity
 *
 * ### Passed args:
 * - integer `Involvement` The involvement id to get the roster from
 * 
 * @param integer $to The involvement to invite them to
 * @todo Don't invite users who are already on the roster! (move to model?)
 */
	function invite_roster($to = null) {
		$from = $this->passedArgs['Involvement'];

		// get from roster
		$this->Involvement->contain(array('Roster'));
		$roster = $this->Involvement->read(null, $from);
		$userIds = Set::extract('/Roster/user_id', $roster);
		
		$this->Involvement->Roster->User->contain(array('Profile'));
		$this->set('notifier', $this->Involvement->Roster->User->read(null, $this->activeUser['User']['id']));

		$this->Notifier->saveData = array('type' => 'invitation');
		foreach ($userIds as $userId) {
			$this->Involvement->contain(array('InvolvementType'));
			$this->set('involvement', $this->Involvement->read(null, $to));

			$this->Notifier->notify($userId, 'involvements_invite');
			$this->QueueEmail->send(array(
				'to' => $userId,
				'subject' => 'Invitation',
				'template' => 'involvements_invite'
			));
			
			$this->Session->setFlash('The user was invited.', 'flash'.DS.'success');			
		}
		
		$this->redirect(array('action' => 'view', $to));
	}
	
/**
 * Invites a user to an involvement opportunity
 *
 * ### Passed args:
 * - `Involvement` The involvement id to invite the user to
 *
 * @param integer $userId The user to invite
 * @todo Don't invite users who are already on the roster! (move to model?)
 */ 
	function invite($userId = null) {
		$involvementId = $this->passedArgs['Involvement'];

		// create notification from template
		$this->Involvement->Roster->User->contain(array('Profile'));
		$this->Involvement->contain(array('InvolvementType'));
		$this->set('notifier', $this->Involvement->Roster->User->read(null, $this->activeUser['User']['id']));
		$this->set('involvement', $this->Involvement->read(null, $involvementId));

		$this->Notifier->saveData = array('type' => 'invitation');
		$this->Notifier->notify($userId, 'involvements_invite');
		$this->QueueEmail->send(array(
			'to' => $userId,
			'subject' => 'Invitation',
			'template' => 'involvements_invite'
		));
		
		$this->redirect(array('action' => 'view', $involvementId));
	}
	
/**
 * Adds an involvement opportunity
 *
 * By default, Involvement is inactive until Involvement::toggleActivity() is called. Additional
 * validation is performed then.
 */
	function add() {
		$this->Involvement->Behaviors->disable('Confirm');
		
		if (!empty($this->data)) {
			$this->Involvement->create();
			if ($this->Involvement->save($this->data)) {
				$this->Session->setFlash('Involvement opportunity saved!', 'flash'.DS.'success');
				$this->redirect(array('action' => 'edit', 'Involvement' => $this->Involvement->id));
			} else {
				$this->Session->setFlash('The involvement could not be saved. Please, try again.', 'flash'.DS.'failure');
			}
		}
				
		$this->set('ministries', $this->Involvement->Ministry->find('list'));
		$this->set('displayMinistries', array($this->Involvement->Ministry->find('list')));
		$this->set('involvementTypes', $this->Involvement->InvolvementType->find('list'));
	}
	
/**
 * Edits an involvement opportunity
 */
	function edit() {
		$id = $this->passedArgs['Involvement'];
	
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Invalid involvement');
			$this->redirect(array('action' => 'index'));
		}

		// if they can confirm a revision, there's no need to go through the confirmation process
		if ($this->isAuthorized('involvements/revise')) {
			$this->Involvement->Behaviors->disable('Confirm');
		}		
		
		$this->Involvement->id = $id;
		$revision = $this->Involvement->revision($id);
		
		if (!empty($this->data)) {
			if (!$revision) {
				if ($this->Involvement->save($this->data)) {
					$this->Session->setFlash('The changes to this involvement opportunity are pending.', 'flash'.DS.'success');
					$this->redirect(array('action' => 'view', 'Involvement' => $id));
				} else {
					$this->Session->setFlash('There were problems saving the changes.', 'flash'.DS.'failure');
				}
				
				$revision = $this->Involvement->revision($id);
			} else {
				$this->Session->setFlash('There\'s already a pending revision for this involvement opportunity.', 'flash'.DS.'failure');
			}		
		}
		if (empty($this->data)) {
			$this->data = $this->Involvement->read(null, $id);			
		}
		
		$this->set('groups', $this->Involvement->Group->find('list', array(
			'conditions' => array(
				'Group.conditional' => false
			)
		)));
		$this->set('ministries', $this->Involvement->Ministry->find('list'));
		$this->set('displayMinistries', array($this->Involvement->Ministry->find('list')));
		$this->set('involvementTypes', $this->Involvement->InvolvementType->find('list'));
		$this->set('revision', $revision);
	}

/**
 * Toggles the `active` field for an involvement
 *
 * ### Requirements:
 * - At least 1 leader must be added
 * - If the involvement takes payment, at least 1 PaymentOption must be defined
 *
 * @param boolean $active Whether to make the model inactive or active
 * @param boolean $recursive Whether to iterate through the model's relationships and mark them as $active
 */
	function toggle_activity($active = false, $recursive = false) {
		$id = $this->passedArgs['Involvement'];
		
		if (!$id) {
			$this->Session->setFlash('Invalid id', 'flash'.DS.'failure');
			$this->redirect(array('action' => 'edit', $id));
		}
		
		// get involvement
		$this->Involvement->contain(array('PaymentOption', 'Leader'));
		$involvement = $this->Involvement->read(null, $id);
		if ($involvement['Involvement']['take_payment'] && $active) {
			if (empty($involvement['PaymentOption'])) {
				$this->Session->setFlash('Cannot activate until a payment option is defined', 'flash'.DS.'failure');
				$this->redirect($this->emptyPage);
				return;
			}
		}
		if (empty($involvement['Leader']) && $active) {
			$this->Session->setFlash('Cannot activate until a leader is added', 'flash'.DS.'failure');
			$this->redirect($this->emptyPage);
			return;
		}
		
		$this->Involvement->Behaviors->disable('Confirm');
		$success = $this->Involvement->toggleActivity($id, $active, $recursive);
		$this->Involvement->Behaviors->enable('Confirm');
		
		if ($success) {
			$this->Session->setFlash(
				'Successfully '.($active ? 'activated' : 'deactivated')				
				.' Involvement '.$id.' '
				.($recursive ? ' and all related items' : ''),
				'flash'.DS.'success'
			);
		} else {
			$this->Session->setFlash(
				'Failed to '.($active ? 'activate' : 'deactivate')				
				.' Involvement '.$id.' '
				.($recursive ? ' and all related items' : ''),
				'flash'.DS.'failure'
			);
		}
		$this->data = array();
		$this->redirect($this->emptyPage);
	}
	
		
/**
 * Displays involvement revision history (up to 1 change)
 */ 	
	function history() {
		$id = $this->passedArgs['Involvement'];
		
		if (!$id) {
			$this->Session->setFlash('Invalid involvement');
			$this->redirect(array('action' => 'index'));
		}
		
		$this->set('involvement', $this->Involvement->read(null, $id));
		
		// get the most recent change (not quite using revisions as defined, but close)
		$this->set('groups', $this->Involvement->Group->find('list'));
		$this->set('ministries', $this->Involvement->Ministry->find('list'));
		$this->set('involvementTypes', $this->Involvement->InvolvementType->find('list'));
		$this->set('revision', $this->Involvement->revision($id));
	}
	

/**
 * Revises a involvement (confirm or deny revision)
 *
 * @param boolean $confirm Whether this is a confirmation or denial
 */ 	
	function revise($confirm = false) {
		$id = $this->passedArgs['Involvement'];
		
		if ($confirm) {
			$success = $this->Involvement->confirmRevision($id);
		} else {
			$success = $this->Involvement->denyRevision($id);
		}
		
		if ($success) {
			$this->Session->setFlash('Action taken');
		} else {
			$this->Session->setFlash('Error');
		}
		
		$this->redirect(array('action' => 'history', 'Involvement' => $id));
	}

	
/**
 * Deletes an involvement opportunity
 *
 * @param integer $id The id of the involvement to delete
 */
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for involvement', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Involvement->delete($id)) {
			$this->Session->setFlash(__('Involvement deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Involvement was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
		
/**
 * Runs a search on simple fields (name)
 *
 * ### Params:
 * - Every named parameter is treated as an "action". Each action should have a key 
 * value pair. The key is the name to display, value is the js function to run (no parens).
 * The selected user id is always passed as the first param to the js function
 *
 * ### Filters: Everything passed as an argument are considered filters.
 * Filters are used to help pre-filter the results (i.e., don't show people 
 * who are in a specific household). Passed like filter:[filter] [Model].[field] [value]
 * Example: not HouseholdMember.household_id 12
 * - Filters of the same model are grouped together, i.e., is Leader.model ministry AND not Leader.model_id 1
 * would produce a condition (Leader.model="ministry" AND Leader.model_id<>1) ...
 * - "NOT" filters also produce a OR NULL condition so that users without records still show up
 */
	function simple_search() {
		$filters = func_get_args();
		$allowedFilters = array('in', 'not', 'is');
		
		$results = array();
		$searchRan = false;

		if (!empty($this->data)) {
			$conditions = array();
			// at the very least, we want:
			$contain = array(
				'InvolvementType' => array()
			);			
			// create conditions
			foreach ($this->data as $model => $fields) {
				foreach ($fields as $key => $value) {
					if ($value != '') {
						$conditions[$model.'.'.$key.' like'] = '%'.$value.'%';
						// use it in the find
						$contain[$model] = array();
					}
				}
			}
			
			// add filters
			$tables = array();	
			$joinConditions = array();
			$nullConditions = array();
			foreach ($filters as $filter) {				
				$filter = explode(' ', $filter);
				// get filter info
				list($filter, $modelField, $modelId) = $filter;
				$modelField = explode('.', $modelField);
				$model = $modelField[0];
				$field = $modelField[1];				
					
				if (in_array($filter, $allowedFilters) && $model != 'Involvement') {
					// workaround for now					
					if ($this->Involvement->{$model}->isVirtualField($field)) {
						$conditionField = $this->Involvement->{$model}->getVirtualField($field);
					} else {
						$conditionField = $model.'.'.$field;
					}
					
					// temp belongsTo (to join, use conditions, etc.)
					$hasOne = array(
						$model => array(
							'className' => Inflector::classify($model),
							'foreignKey' => 'involvement_id',
							'conditions' => array(
								$conditionField => $modelId
							)
						)
					);
					
					// merge, just in case it already exists
					$this->Involvement->hasOne = Set::merge($this->Involvement->hasOne, $hasOne);

					$contain[$model] = array();
					
					switch ($filter) {
						case 'in':
							$joinConditions[$model][$conditionField] = array($modelId);
						break;
						case 'is':
							$joinConditions[$model][$conditionField] = $modelId;
						break;
						case 'not':						
							// add the null condition for users without a record
							$joinConditions[$model][$conditionField.' <>'] = $modelId;
							$nullConditions[$model][$conditionField] = null;
						break;
					}
				} elseif ($model == 'Involvement') {
					if ($this->Involvement->isVirtualField($field)) {
						$conditionField = $this->Involvement->getVirtualField($field);
					} else {
						$conditionField = $model.'.'.$field;
					}
					
					switch ($filter) {
						case 'in':
							$joinConditions[$model][$conditionField] = array($modelId);
						break;
						case 'is':
							$joinConditions[$model][$conditionField] = $modelId;
						break;
						case 'not':						
							// add the null condition for users without a record
							$joinConditions[$model][$conditionField.' <>'] = $modelId;
							$nullConditions[$model][$conditionField] = null;
						break;
					}
				}
			}

			foreach ($joinConditions as $modelVal => $modelConditions) {
				// combine model conditions with null conditions to prevent misleading
				// results due to lack of records
				if (!empty($nullConditions[$modelVal])) {
					$conditions[] = array(
						'or' => array($joinConditions[$modelVal], $nullConditions[$modelVal])
					);
				} else {
					$conditions[] = $modelConditions;
				}
			}
			
			// Involvement can't contain Involvement!
			unset($contain['Involvement']);
			$this->paginate = compact('conditions', 'contain');
			$searchRan = true;
		}
		
		$results = $this->FilterPagination->paginate();
		
		$this->set('filters', implode(',',$filters));
		// remove pagination info from action list
		$actions = array_diff_key($this->params['named'], array('page'=>array(),'sort'=>array(),'direction'=>array()));
		$this->set(compact('results','searchRan','actions'));
	}
}
?>