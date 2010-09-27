<?php
/**
 * Profile model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Profile model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Profile extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Profile';

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	var $actsAs = array(
		'Logable',
		'Containable',
		'Search.Searchable'
	);

/**
 * Virtual field definitions
 *
 * @var array
 */
	var $virtualFields = array(
		'name' => 'CONCAT(:ALIAS:.first_name, " ", :ALIAS:.last_name)',
		'age' => 'DATEDIFF(CURDATE(), :ALIAS:.birth_date)/365.25',
		'child' => 'IF (:ALIAS:.adult = 1, 0, IF (:ALIAS:.birth_date IS NULL, 1, ((DATE_FORMAT(NOW(),"%Y") - DATE_FORMAT(:ALIAS:.birth_date,"%Y")) < 18)))',
		'leading' => 'SELECT COUNT(1) FROM leaders as LeadingLeader JOIN involvements AS LeadingInvolvement ON (LeadingInvolvement.id = LeadingLeader.model_id AND LeadingLeader.model = "Involvement") WHERE LeadingLeader.user_id = :ALIAS:.user_id AND LeadingInvolvement.active = 1',
		'managing' => 'SELECT COUNT(1) FROM leaders as ManagingLeader JOIN involvements AS ManagingInvolvement ON (ManagingInvolvement.id = ManagingLeader.model_id AND ManagingLeader.model IN ("Ministry", "Campus")) WHERE ManagingLeader.user_id = :ALIAS:.user_id AND ManagingInvolvement.active = 1'
	);

/**
 * Validation rules
 *
 * @var array
 */
	var $validate = array(
		'first_name' => array('rule' => 'notempty'),
		'last_name' => array('rule' => 'notempty'),
		'gender_name' => array(
			'rule' => array('inList', array('m','f')),
			/*'required' => false,*/
			'allowEmpty' => true
		),
		'birth_date' => array(
			'rule' => 'date',
			'required' => false,
			'allowEmpty' => true
		),
		'marital_status' => array(
			'rule' => array('inList', array('m','s','d','w')),
			'required' => false,
			'allowEmpty' => true
		),
		'job_name' => array(
			'rule' => 'alphaNumeric',
			'required' => false,
			'allowEmpty' => true,
			'message' => 'Letters and numbers only, please.'
		),
		'cell_phone' => array(
			'rule' => array('phone', null, 'us'),
			'required' => false,
			'allowEmpty' => true,
			'message' => 'Must be a phone number.'
		),
		'home_phone' => array(
			'rule' => array('phone', null, 'us'),
			'required' => false,
			'allowEmpty' => true,
			'message' => 'Must be a phone number.'
		),
		'work_phone' => array(
			'rule' => array('phone', null, 'us'),
			'required' => false,
			'allowEmpty' => true,
			'message' => 'Must be a phone number.'
		),
		'primary_email' => array(
			'email' => array(
				'rule' => 'email',
				'message' => 'Must be a valid email address.'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'That email address is being used by another member.'
			),
			'required' => true
		),
		'alternate_email_1' => array(
			'email' => array(
				'rule' => 'email',
				'required' => false	,
				'allowEmpty' => true,
				'message' => 'Must be a valid email address.'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'That email address is being used by another member.'
			)
		),
		'alternate_email_2' => array(
			'email' => array(
				'rule' => 'email',
				'required' => false	,
				'allowEmpty' => true,
				'message' => 'Must be a valid email address.'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'That email address is being used by another member.'
			)
		)
	);

/**
 * BelongsTo association link
 *
 * @var array
 */
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Classification' => array(
			'className' => 'Classification',
			'foreignKey' => 'classification_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'JobCategory' => array(
			'className' => 'JobCategory',
			'foreignKey' => 'job_category_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Creator' => array(
			'className' => 'User',
			'foreignKey' => 'created_by',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Campus',		
		'ElementarySchool' => array(
			'className' => 'School',
			'foreignKey' => 'elementary_school_id'
		),		
		'MiddleSchool' => array(
			'className' => 'School',
			'foreignKey' => 'middle_school_id'
		),		
		'HighSchool' => array(
			'className' => 'School',
			'foreignKey' => 'high_school_id'
		),		
		'College' => array(
			'className' => 'School',
			'foreignKey' => 'college_id'
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
			'name' => 'simple',
			'type' => 'query',
			'method' => 'makeFulltext',
			'field' => array(
				'Profile.first_name',
				'Profile.last_name',
			)
		)
	);

/**
 * Model::beforeValidate callback
 *
 * @return true Continue with save
 */
	function beforeValidate() {
		// clear out extra characters in phone numbers
		if (isset($this->data['Profile']['cell_phone'])) {
			$this->data['Profile']['cell_phone'] = preg_replace('/[^0-9]+/', '', $this->data['Profile']['cell_phone']);
		}
		if (isset($this->data['Profile']['home_phone'])) {
			$this->data['Profile']['home_phone'] = preg_replace('/[^0-9]+/', '', $this->data['Profile']['home_phone']);
		}
		if (isset($this->data['Profile']['work_phone'])) {
			$this->data['Profile']['work_phone'] = preg_replace('/[^0-9]+/', '', $this->data['Profile']['work_phone']);
		}
		
		return true;
	}	
}
?>