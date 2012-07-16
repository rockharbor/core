<?php
/**
 * Group model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Group model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Group extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Group';

/**
 * HasMany association link
 *
 * @var array
 */
	var $hasMany = array(
		'User'
	);

/**
 * Default order
 *
 * @var string
 */
	var $order = ':ALIAS:.parent_id ASC';

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	var $actsAs = array(
		'Acl' => 'requester',
		'Tree'
	);		

/**
 * Finds the parent of this group for Acl
 *
 * This function is only needed to save and edit groups, however AclBehavior
 * throws an error when it doesn't exist.
 *
 * @return mixed The parent, or null if none
 * @access public
 */
	function parentNode() {

	}

/**
 * Convenience function for getting a list of non-conditional group ids
 *
 * @param integer $groupId The group get groups above/below from
 * @param string $operator Find groups that are less than, greater than, less
 *		than equal, etc. Where '>' means 'of higher permission'
 * @return array Array of ids
 */
	function findGroups($groupId = null, $operator = '<=') {
		if (!$groupId) {
			return false;
		}

		$operatorMap = array(
			'<' => '>',
			'>' => '<',
			'<=' => '>=',
			'>=' => '<=',
			'=' => '='
		);

		$groups = $this->find('all', array(
			'fields' => array(
				'id'
			),
			'conditions' => array(
				'Group.conditional' => false,
				'Group.lft '.$operatorMap[$operator] => $groupId
			)
		));
		return Set::extract('/Group/id', $groups);
	}

/**
 * Determines whether a user in a group can see private records
 *
 * @param integer $groupId
 * @return boolean
 */
	function canSeePrivate($groupId = null) {
		if (!$groupId) {
			return false;
		}

		$groups = $this->findGroups(Core::read('general.private_group'), '>');
		
		return in_array($groupId, $groups);
	}
}
