<?php
/**
 * Alert model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Alert model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Alert extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Alert';

/**
 * Behaviors for this model
 *
 * @var array
 */
	var $actsAs = array(
		'Cacher.Cache' => array(
			'duration' => '+1 day',
			'auto' => true
		)
	);

/**
 * Validation rules
 *
 * @var array
 */
	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => 'notempty',
				'message' => 'Please fill in the required field.'
			)
		),
		'description' => array(
			'notempty' => array(
				'rule' => 'notempty',
				'message' => 'Please fill in the required field.'
			)
		),
		'group_id' => array(
			'rule' => 'numeric',
			'allowEmpty' => false
		),
		'expires' => array(
			'date' => array(
				'rule' => 'date',
				'message' => 'Please select a valid date',
				'allowEmpty' => true
			)			
		)
	);

/**
 * BelongsTo association link
 *
 * @var array
 */
	var $belongsTo = array(
		'Group'
	);

/**
 * HasAndBelongsToMany association link
 *
 * @var array
 */
	var $hasAndBelongsToMany = array(
		'User' => array(
			'className' => 'User',
			'joinTable' => 'alerts_users',
			'foreignKey' => 'alert_id',
			'associationForeignKey' => 'user_id',
			'dependent' => true
		)
	);

/**
 * Gets ids of alerts that have been read by a user
 *
 * @param integer $userId The user
 * @return array List of ids
 */ 
	function getReadAlerts($userId = null) {
		if (!$userId) {
			return false;
		}		
		
		// get ids of alerts this user has read		
		$readAlerts = $this->AlertsUser->find('all', array(
			'conditions' => array(
				'user_id' => $userId
			)
		));
		
		return Set::extract('/AlertsUser/alert_id', $readAlerts);
	}

/**
 * Gets ids of alerts that have not been read by a user
 *
 * @param integer $userId The user
 * @param array $groupId The user's group id
 * @param boolean $getExpired Whether or not to get expired alerts as well
 * @return array List of ids
 */ 	
	function getUnreadAlerts($userId = null, $groupId = null, $getExpired = true) {
		if (!$userId) {
			return false;
		}
		if (!$groupId) {
			$groups = $this->Group->findByName('User');
			$groups = $groups['Group']['id'];
		} else {
			$groups = array_keys($this->Group->findGroups($groupId));
		}
		// get ids of alerts this user has read
		$readAlerts = $this->getReadAlerts($userId);
		
		$search = array(
			'conditions' => array(
				'not' => array(
					'Alert.id' => $readAlerts
				),
				'Group.id' => $groups
			),
			'order' => 'Alert.created DESC'
		);
		
		if (!$getExpired) {
			$search['conditions']['or'] = array(
				'Alert.expires >=' => date('Y-m-d'),
				'Alert.expires' => null
			);
		}
		$this->recursive = 0;
		$alert = $this->find('all', $search);
		
		return Set::extract('/Alert/id', $alert);
	}

/**
 * Marks an Alert as read by a User
 *
 * @param integer $userId The user
 * @param integer $alertId The alert 
 * @return boolean Success
 */ 	
	function markAsRead($userId = null, $alertId = null) {
		if (!$userId || !$alertId) {
			return false;
		}

		// make sure alert exists
		$this->id = $alertId;
		$this->Group->User->id = $userId;
		if (!$this->exists() || !$this->Group->User->exists()) {
			return false;
		}

		// get read alerts
		$readAlerts = $this->getReadAlerts($userId);
		
		if (!in_array($alertId, $readAlerts)) {
			$this->AlertsUser->create();
			return $this->AlertsUser->save(array(
				'alert_id' => $alertId,
				'user_id' => $userId
			));
		} else {
			return true;
		}
	}
}
?>