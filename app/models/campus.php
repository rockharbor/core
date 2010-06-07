<?php
class Campus extends AppModel {
	var $name = 'Campus';
	
	var $actsAs = array(
		'Logable',
		'Revision',
		'Containable'
	);

	var $hasMany = array(
		'Ministry' => array(
			'className' => 'Ministry',
			'foreignKey' => 'campus_id',
			'dependent' => false
		),
		'Leader' => array(
			'className' => 'Leader',
			'foreignKey' => 'model_id',
			'dependent' => false,
			'conditions' => array('Leader.model' => 'Campus')
		)
	);
	
	var $hasOne = array(
		'Image' => array(
			'className' => 'Attachment',
			'foreignKey' => 'foreign_key',
			'dependent' => false,
			'conditions' => array('Image.model' => 'Campus')
		)
	);
	
/**
 * Checks if a user is a manager for a campus
 *
 * @param integer $userId The user id
 * @param integer $campusId The campus id
 * @return boolean True if the user is a manager
 * @access public
 */ 
	function isManager($userId = null, $campusId = null) {
		if (!$userId || !$campusId) {
			return false;
		}
		
		return $this->Leader->hasAny(array(
			'model' => 'Campus',
			'model_id' => $campusId,
			'user_id' => $userId
		));
	}

}
?>