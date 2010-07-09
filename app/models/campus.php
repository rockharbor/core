<?php
/**
 * Campus model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Campus model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Campus extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Campus';

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	var $actsAs = array(
		'Logable',
		'Containable'
	);

/**
 * HasMany association link
 *
 * @var array
 */
	var $hasMany = array(
		'Ministry' => array(
			'className' => 'Ministry',
			'foreignKey' => 'campus_id',
			'dependent' => true
		),
		'Leader' => array(
			'className' => 'Leader',
			'foreignKey' => 'model_id',
			'dependent' => true,
			'conditions' => array('Leader.model' => 'Campus')
		)
	);

/**
 * HasOne association link
 *
 * @var array
 */
	var $hasOne = array(
		'Image' => array(
			'className' => 'Attachment',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
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