<?php
class Ministry extends AppModel {
	var $name = 'Ministry';
		
	var $actsAs = array(
		'Logable',
		'Containable',
		'Tree',
		'Confirm'
	);
	
	var $validate = array(
		'name' => array(	
			'rule' => 'notempty'
		),
		'description' => array(	
			'rule' => 'notempty'
		)
	);

	var $belongsTo = array(
		'ParentMinistry' => array(
			'className' => 'Ministry',
			'foreignKey' => 'parent_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Campus' => array(
			'className' => 'Campus',
			'foreignKey' => 'campus_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Group'
	);
	
	var $hasOne = array(
		'Image' => array(
			'className' => 'Attachment',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array('Image.model' => 'Ministry')
		)
	);

	var $hasMany = array(
		'Involvement' => array(
			'className' => 'Involvement',
			'foreignKey' => 'ministry_id',
			'dependent' => true
		),
		'ChildMinistry' => array(
			'className' => 'Ministry',
			'foreignKey' => 'parent_id',
			'dependent' => true
		),
		'Role' => array(
			'className' => 'Role',
			'foreignKey' => 'ministry_id',
			'dependent' => true
		),
		'Leader' => array(
			'className' => 'Leader',
			'foreignKey' => 'model_id',
			'dependent' => true,
			'conditions' => array('Leader.model' => 'Ministry')
		)
	);
	
	

/**
 * Checks if a user is a manager for a ministry
 *
 * @param integer $userId The user id
 * @param integer $ministryId The ministry id
 * @return boolean True if the user is a manager
 * @access public
 */ 
	function isManager($userId = null, $ministryId = null) {
		if (!$userId || !$ministryId) {
			return false;
		}
		
		return $this->Leader->hasAny(array(
			'model' => 'Ministry',
			'model_id' => $ministryId,
			'user_id' => $userId
		));
	}
}
?>