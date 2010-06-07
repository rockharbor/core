<?php
class Alert extends AppModel {
	var $name = 'Alert';
	
	var $displayField = 'name';
	
	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => 'notempty',
				'message' => 'Please fill in the name.'
			)
		),
		'description' => array(
			'notempty' => array(
				'rule' => 'notempty',
				'message' => 'Please fill in the alert text.'
			)
		),
		'group_id' => array(
			'rule' => 'numeric',
			'allowEmpty' => false
		),
		'importance' => array(
			'inlist' => array(
				'rule' => array('inlist', array('low', 'medium', 'high'))
			)
		),
		'expires' => array(
			'date' => array(
				'rule' => 'date',
				'message' => 'Please select a valid date',
				'allowEmpty' => true
			)			
		)
	);
	
	var $belongsTo = array(
		'Group'
	);
	
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
 * @param array $groupIds Array of Alert group ids to check for
 * @return array List of ids
 */ 	
	function getUnreadAlerts($userId = null, $groupIds = array(), $getExpired = true) {
		if (!$userId || empty($groupIds)) {
			return false;
		}
		
		// get ids of alerts this user has read
		$readAlerts = $this->getReadAlerts($userId);
		
		$this->recursive = -1;
		
		$search = array(
			'conditions' => array(
				'not' => array(
					'Alert.id' => $readAlerts
				),
				'Alert.group_id' => $groupIds
			),
			'order' => 'Alert.created DESC'
		);
		
		if (!$getExpired) {
			$search['conditions']['or'] = array(
				'Alert.expires >=' => date('Y-m-d'),
				'Alert.expires' => null
			);
		}
		
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