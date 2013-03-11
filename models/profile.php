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
	public $name = 'Profile';

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	public $actsAs = array(
		'Containable',
		'Search.Searchable',
		'Logable'
	);

/**
 * Virtual field definitions
 *
 * @var array
 */
	public $virtualFields = array(
		'name' => 'CONCAT(:ALIAS:.first_name, " ", :ALIAS:.last_name)',
		'age' => 'DATEDIFF(CURDATE(), :ALIAS:.birth_date)/365.25',
		'child' => 'IF (:ALIAS:.adult = 1, 0, IF (:ALIAS:.birth_date IS NULL, 1, ((DATE_FORMAT(NOW(),"%Y") - DATE_FORMAT(:ALIAS:.birth_date,"%Y")) < 18)))',
		'leading' => 'SELECT COUNT(LeadingLeader.id) FROM leaders as LeadingLeader WHERE LeadingLeader.user_id = :ALIAS:.user_id AND LeadingLeader.model = "Involvement"',
		'managing' => 'SELECT COUNT(ManagingLeader.id) FROM leaders as ManagingLeader WHERE ManagingLeader.user_id = :ALIAS:.user_id AND (ManagingLeader.model = "Campus" OR ManagingLeader.model = "Ministry")'
	);

/**
 * Validation rules
 *
 * Note: birth_date 'allowEmpty' is set to `false` in `UsersController::register()`
 * and `UsersController::household_add()`
 *
 * @var array
 */
	public $validate = array(
		'first_name' => array(
			'rule' => 'notempty',
			'message' => 'Please fill in the required field.'
		),
		'last_name' => array(
			'rule' => 'notempty',
			'message' => 'Please fill in the required field.'
		),
		'gender_name' => array(
			'rule' => array('inList', array('m','f')),
			'allowEmpty' => true
		),
		'birth_date' => array(
			'rule' => 'date',
			'required' => false,
			'allowEmpty' => true,
			'message' => 'Please enter a valid date.'
		),
		'job_name' => array(
			'rule' => array('custom', '/^[a-z0-9 ]*$/i'),
			'required' => false,
			'allowEmpty' => true,
			'message' => 'Please use alpha and numeric characters only.'
		),
		'cell_phone' => array(
			'rule' => '/^[0-9]{7,10}$/',
			'required' => false,
			'allowEmpty' => true,
			'message' => 'Please enter a valid phone number.'
		),
		'home_phone' => array(
			'rule' => '/^[0-9]{7,10}$/',
			'required' => false,
			'allowEmpty' => true,
			'message' => 'Please enter a valid phone number.'
		),
		'work_phone' => array(
			'rule' => '/^[0-9]{7,10}$/',
			'required' => false,
			'allowEmpty' => true,
			'message' => 'Please enter a valid phone number.'
		),
		'primary_email' => array(
			'email' => array(
				'rule' => 'email',
				'message' => 'Please enter a valid email address.',
				'required' => false,
				'allowEmpty' => true
			),
		),
		'alternate_email_1' => array(
			'email' => array(
				'rule' => 'email',
				'required' => false,
				'allowEmpty' => true,
				'message' => 'Please enter a valid email address.'
			),
		),
		'alternate_email_2' => array(
			'email' => array(
				'rule' => 'email',
				'required' => false,
				'allowEmpty' => true,
				'message' => 'Please enter a valid email address.'
			),
		)
	);

/**
 * BelongsTo association link
 *
 * @var array
 */
	public $belongsTo = array(
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
 * Model::beforeValidate callback
 *
 * @return true Continue with save
 */
	public function beforeValidate() {
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
