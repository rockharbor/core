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
		'Confirm',
		'Containable',
		'Cacher.Cache' => array(
			'auto' => false
		)
	);

/**
 * Validation rules
 *
 * @var array
 */
	var $validate = array(
		'name' => array(
			'rule' => 'notempty'
		),
		'description' => array(
			'rule' => 'notempty'
		)
	);

/**
 * Sanitization rules
 *
 * @var array
 * @see Sanitizer.SanitizeBehavior
 */
	var $sanitize = array(
		'description' => 'stripScripts'
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

/**
 * Gets all users involved in all involvements within a campus
 *
 * @param integer $ministryId The ministry id
 * @param boolean $recursive Whether to pull for subministries as well
 * @return array The user ids
 */
	function getInvolved($campusId, $recursive = false) {
		$ministries = $this->Ministry->find('list', array(
			'conditions' => array(
				'Ministry.campus_id' => $campusId,
				'or' => array(
					'Ministry.parent_id' => null,
					'or' => array(
						'Ministry.parent_id' => 0,
					)
				)
			)
		));
		return array_unique($this->Ministry->getInvolved(array_keys($ministries), $recursive));
	}

/**
 * Gets all leaders of all involvements within a campus
 *
 * @param integer $ministryId The ministry id
 * @param boolean $recursive Whether to pull for subministries as well
 * @return array The user ids
 */
	function getLeaders($campusId, $recursive = false) {
		$ministries = $this->Ministry->find('list', array(
			'conditions' => array(
				'Ministry.campus_id' => $campusId,
				'or' => array(
					'Ministry.parent_id' => null,
					'or' => array(
						'Ministry.parent_id' => 0,
					)
				)
			)
		));
		return array_unique($this->Ministry->getLeaders(array_keys($ministries), $recursive));
	}
}
?>