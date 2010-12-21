<?php
/**
 * Comment model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Comment model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Comment extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Comment';

/**
 * Behaviors for this model
 *
 * @var array
 */
	var $actsAs = array(
		'Containable',
		'Linkable.AdvancedLinkable'
	);

/**
 * Validation rules
 *
 * @var array
 */
	var $validate = array(
		'comment' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
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
		'Creator' => array(
			'className' => 'User',
			'foreignKey' => 'created_by',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'group_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * Checks of a user can delete a comment
 *
 * @param integer $userId The user id
 * @param integer $id The id of the comment
 * @return boolean
 */
	function canDelete($userId = null, $id = null) {
		if (!$userId || !$id) {
			return false;
		}
		$user = $this->User->read(array('group_id'), $userId);
		if (!$user) {
			return false;
		}
		$this->contain(array(
			'Creator' => array(
				'fields' => array('id', 'group_id')
			)
		));
		$groups = $this->Group->findGroups($user['User']['group_id'], 'list', '<=');
		// can delete if they are in a higher/equal group or they created it
		$comment = $this->find('first', array(
			'fields' => array(
				'id', 'user_id'
			),
			'conditions' => array(
				'Comment.id' => $id,
				'or' => array(
					'Creator.group_id' => array_keys($groups),
					'Creator.id' => $userId,
				)
			)
		));

		if ($comment) {
			// cannot remove comments on themselves, unless they created it
			if ($comment['Comment']['user_id'] == $userId) {
				return $comment['Comment']['user_id'] == $comment['Creator']['id'];
			}
			return true;
		}
		return false;
	}

/**
 * Checks of a user can edit a comment. Currently the same as Comment::canDelete()
 *
 * @param integer $userId The user id
 * @param integer $id The id of the comment
 * @return boolean
 * @see Comment::canDelete()
 */
	function canEdit($userId = null, $id = null) {
		return $this->canDelete($userId, $id);
	}
}
?>