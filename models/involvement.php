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
	public $name = 'Involvement';

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
	public $virtualFields = array(
		'previous' => 'NOT EXISTS(
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
	public $sanitize = array(
		'description' => 'stripScripts'
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => 'Please fill in the required field.'
		),
		'description' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => 'Please fill in the required field.'
		)
	);

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	public $actsAs = array(
		'Containable',
		'Sanitizer.Sanitize',
		'Search.Searchable',
		'Linkable.AdvancedLinkable',
		'Logable'
	);

/**
 * HasOne association link
 *
 * @var array
 */
	public $hasOne = array(
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
	public $belongsTo = array(
		'Ministry' => array(
			'className' => 'Ministry',
			'foreignKey' => 'ministry_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'InvolvementType',
		'DefaultStatus' => array(
			'className' => 'RosterStatus',
			'foreignKey' => 'default_status_id'
		)
	);

/**
 * HasMany association link
 *
 * @var array
 */
	public $hasMany = array(
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
			'conditions' => array('Image.model' => 'Involvement', 'Image.group' => 'Image', 'Image.approved' => true)
		)
	);

/**
 * HasAndBelongsToMany association link
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
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
	public $searchFilter = array(
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
		),
		'canBePromoted' => array(
			'conditions' => array(
				'Image.approved' => true,
				'Image.promoted' => false
			),
			'link' => array(
				'Image'
			)
		)
	);

/**
 * Checks if a user is a leader for an involvement
 *
 * @param integer $userId The user id
 * @param integer $involvementId The involvement id
 * @return boolean True if the user is a leader
 */
	public function isLeader($userId = null, $involvementId = null) {
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
	public function getInvolved($involvementId) {
		$results = $this->Roster->find('all', array(
			'fields' => array(
				'user_id'
			),
			'conditions' => array(
				'Roster.involvement_id' => $involvementId
			),
			'fields' => array('user_id')
		));
		return Set::extract('/Roster/user_id', $results);
	}

/**
 * Gets the leaders for an Involvement
 *
 * @param mixed $modelId Integer for single id, or array for multiple
 * @return array Array of user ids
 */
	public function getLeaders($modelId) {
		$leaders = $this->Leader->find('all', array(
			'fields' => array(
				'user_id'
			),
			'conditions' => array(
				'Leader.model' => 'Involvement',
				'Leader.model_id' => $modelId
			)
		));
		$ids = Set::extract('/Leader/user_id', $leaders);
		return array_unique($ids);
	}
}
