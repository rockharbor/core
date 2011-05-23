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
				WHERE NotPassed.involvement_id = :ALIAS:.id
				AND (CAST(CONCAT(NotPassed.end_date, " ", NotPassed.end_time) AS DATETIME) > NOW()
				OR NotPassed.permanent = 1)
				AND NotPassed.exemption = 0
		) AND EXISTS (SELECT 1 FROM dates as ExistingDates WHERE ExistingDates.involvement_id = :ALIAS:.id)',
		'has_dates' => 'EXISTS (SELECT 1 FROM dates as HasDates WHERE HasDates.involvement_id = :ALIAS:.id)'
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
		'Logable',
		'Sanitizer.Sanitize',
		'Search.Searchable',
		'Linkable.AdvancedLinkable'
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
		'InvolvementType'
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
			'className' => 'Document',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array('Document.model' => 'Involvement', 'Document.group' => 'Document')
		),
		'Image' => array(
			'className' => 'Image',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array('Image.model' => 'Involvement', 'Image.group' => 'Image')
		)
	);

/**
 * HasAndBelongsToMany association link
 *
 * @var array
 */
	var $hasAndBelongsToMany = array(
		'DisplayMinistry' => array(
			'className' => 'Ministry',
			'table' => 'involvements_ministries',
			'foreignKey' => 'involvement_id',
			'associationForeignKey' => 'ministry_id'
		)
	);

/**
 * Array of search filters for SearchesController::simple().
 *
 * They are merged with any existing conditions and parameters sent to
 * Controller::paginate(). Works in conjunction with
 * SearchesController::simple() where arguments sent after the filter name are
 * inserted in order within the filter. Make sure to include contains or links
 * where related model data is needed.
 *
 * @var array
 */
	var $searchFilter = array(
		'notInvolvementAndIsLeading' => array(
			'conditions' => array(
				'Involvement.id <>' => ':0:',
				'EXISTS (SELECT 1 FROM leaders WHERE leaders.model = "Involvement"
					AND leaders.model_id = Involvement.id
					AND leaders.user_id = :1:)'
			)
		),
		'notInvolvement' => array(
			'conditions' => array(
				'Involvement.id <>' => ':0:'
			)
		)
	);

/**
 * Filter args for the Search.Searchable behavior
 *
 * @var array
 * @see Search.Searchable::parseCriteria()
 */
	var $filterArgs = array(
		array(
			'name' => 'simple_fulltext',
			'type' => 'query',
			'method' => 'makeFulltext',
			'field' => array(
				'Involvement.description',
				'Involvement.name'
			)
		),
		array(
			'name' => 'simple',
			'type' => 'query',
			'method' => 'makeLikeConditions',
			'operator' => 'OR',
			'field' => array(
				'Involvement.name',
				'Involvement.description',
			)
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

/**
 * Returns a list of user ids that are involved an involvement
 *
 * @param integer $involvementId The involvement id
 * @return array A list of users
 */
	function getInvolved($involvementId) {
		$results = $this->Roster->find('all', array(
			'conditions' => array(
				'Roster.involvement_id' => $involvementId
			),
			'fields' => array('user_id')
		));
		return Set::extract('/Roster/user_id', $results);
	}

/**
 * Returns a list of user ids that lead an involvement
 *
 * @param integer $involvementId The involvement id
 * @return array A list of users
 */
	function getLeaders($involvementId) {
		$results = $this->Leader->find('all', array(
			'conditions' => array(
				'Leader.model_id' => $involvementId,
				'Leader.model' => 'Involvement'
			),
			'fields' => array('user_id')
		));
		return Set::extract('/Leader/user_id', $results);
	}
}
?>