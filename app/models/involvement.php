<?php
/**
 * Involvement model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Involvement model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Involvement extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Involvement';

/**
 * Virtual field definitions
 *
 * ### Fields
 * - `passed` An involvement is passed if its dates end time has passed and it's
 *		not a permanent recurring date. Involvements with no dates are not
 *		considered passed.
 *
 * @var array
 */
	var $virtualFields = array(
		'passed' => 'NOT EXISTS(
			SELECT 1 FROM dates AS NotPassed
				WHERE NotPassed.involvement_id = Involvement.id
				AND (CAST(CONCAT(NotPassed.end_date, " ", NotPassed.end_time) AS DATETIME) > NOW()
				OR NotPassed.permanent = 1)
				AND NotPassed.exemption = 0
		) AND EXISTS (SELECT 1 FROM dates as ExistingDates WHERE ExistingDates.involvement_id = Involvement.id)'
	);

/**
 * Validation rules
 *
 * @var array
 */
	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'required' => true
		),
		'description' => array(
			'rule' => 'notEmpty',
			'required' => true
		)
	);	

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	var $actsAs = array(
		'Containable',
		'Confirm',
		'Logable'
	);

/**
 * HasOne association link
 *
 * @var array
 */
	var $hasOne = array(
		'Address' => array(
			'className' => 'Address',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array('Address.model' => 'Involvement')
		),
		'Image' => array(
			'className' => 'Media.Attachment',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array('Image.model' => 'Involvement')
		)
	);

/**
 * BelongsTo association link
 *
 * @var array
 */
	var $belongsTo = array(
		'Ministry' => array(
			'className' => 'Ministry',
			'foreignKey' => 'ministry_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'InvolvementType',
		'Group'
	);

/**
 * HasMany association link
 *
 * @var array
 */
	var $hasMany = array(
		'Date' => array(
			'className' => 'Date',
			'foreignKey' => 'involvement_id',
			'dependent' => true
		),
		'PaymentOption' => array(
			'className' => 'PaymentOption',
			'foreignKey' => 'involvement_id',
			'dependent' => true
		),
		'Question' => array(
			'className' => 'Question',
			'foreignKey' => 'involvement_id',
			'dependent' => true
		),
		'Roster' => array(
			'className' => 'Roster',
			'foreignKey' => 'involvement_id',
			'dependent' => true
		),
		'Leader' => array(
			'className' => 'Leader',
			'foreignKey' => 'model_id',
			'dependent' => true,
			'conditions' => array('Leader.model' => 'Involvement')
		),
		'Document' => array(
			'className' => 'Media.Attachment',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array('Document.model' => 'Involvement')
		)
	);

/**
 * Checks if a user is a leader for an involvement
 *
 * @param integer $userId The user id
 * @param integer $involvementId The involvement id
 * @return boolean True if the user is a leader
 * @access public
 */ 
	function isLeader($userId = null, $involvementId = null) {
		if (!$userId || !$involvementId) {
			return false;
		}
		
		return $this->Leader->hasAny(array(
			'model' => 'Involvement',
			'model_id' => $involvementId,
			'user_id' => $userId
		));
	}	
}
?>