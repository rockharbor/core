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
		'Confirm',
		'Cacher.Cache' => array(
			'auto' => false
		),
		'Search.Searchable'
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
		),
		'Image' => array(
			'className' => 'Attachment',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array('Image.model' => 'Ministry')
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
				'Ministry.name',
				'Ministry.description',
			)
		),
		array(
			'name' => 'simple',
			'type' => 'query',
			'method' => 'makeLikeConditions',
			'operator' => 'OR',
			'field' => array(
				'Ministry.name',
				'Ministry.description',
			)
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

/**
 * Gets all users involved in all involvements within a ministry
 *
 * @param integer $ministryId The ministry id
 * @param boolean $recursive Whether to pull for subministries as well
 * @return array The user ids
 */
	function getInvolved($ministryId, $recursive = false) {
		$options = array(
			'conditions' => array(
				'Ministry.id' => $ministryId
			),
			'contain' => array(
				'Roster' => array(
					'fields' => array('user_id')
				),
				'Ministry' => array(
					'fields' => array('id')
				)
			),
			'fields' => array('id', 'ministry_id')
		);
		if ($recursive) {
			$options['conditions'] = array(
				'or' => array(
					'Ministry.id' => $ministryId,
					'Ministry.parent_id' => $ministryId
				)
			);
		}
		$results = $this->Involvement->find('all', $options);
		return array_unique(Set::extract('/Roster/user_id', $results));
	}

/**
 * Gets all leaders of all involvements within a ministry
 *
 * @param integer $ministryId The ministry id
 * @param boolean $recursive Whether to pull for subministries as well
 * @return array The user ids
 */
	function getLeaders($ministryId, $recursive = false) {
		$options = array(
			'conditions' => array(
				'Ministry.id' => $ministryId
			),
			'contain' => array(
				'Involvement' => array(
					'fields' => array('id'),
					'Leader' => array(
						'fields' => array('user_id')
					)
				)
			),
			'fields' => array('id')
		);
		if ($recursive) {
			$options['conditions'] = array(
				'or' => array(
					'Ministry.id' => $ministryId,
					'Ministry.parent_id' => $ministryId
				)
			);
		}
		$results = $this->find('all', $options);
		return array_unique(Set::extract('/Involvement/Leader/user_id', $results));
	}
}
?>