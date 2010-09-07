<?php
/**
 * Ministry model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Ministry model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Ministry extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Ministry';

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	var $actsAs = array(
		'Logable',
		'Containable',
		'Tree',
		'Confirm'
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
		'description' => 'html'
	);

/**
 * BelongsTo association link
 *
 * @var array
 */
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
			'conditions' => array('Image.model' => 'Ministry')
		)
	);


/**
 * HasMany association link
 *
 * @var array
 */
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
 * HasAndBelongsToMany association link
 *
 * @var array
 */
	var $hasAndBelongsToMany = array(
		'DisplayInvolvement' => array(
			'className' => 'Involvement',
			'table' => 'involvements_ministries',
			'foreignKey' => 'ministry_id',
			'associationForeignKey' => 'involvement_id'
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