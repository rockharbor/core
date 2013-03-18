<?php
/**
 * Invitation model class.
 *
 * Invitations contain a HABTM link to associate many users with an invitation.
 * This is used in cases where several people should be sent the invitation (that
 * is, it shows up in their notifications list), but the action is only take once
 * and it is removed from everyone else's notifications list.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Invitation model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Invitation extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	public $name = 'Invitation';

/**
 * BelongsTo association link
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		)
	);

/**
 * HasAndBelongsToMany association link
 */
	public $hasAndBelongsToMany = array(
		'CC' => array(
			'className' => 'User',
			'dependent' => true
		)
	);

/**
 * Sanitization rules
 *
 * @var array
 * @see Sanitizer.SanitizeBehavior
 */
	public $sanitize = array(
		'body' => 'stripScripts',
		'confirm_action' => false,
		'deny_action' => false
	);

/**
 * Gets all invitations for a user, including those connected to him via HABTM
 *
 * @param int $userId The user id
 * @return array Array of invitation ids
 */
	public function getInvitations($userId = null) {
		if (!$userId) {
			return array();
		}
		$habtm = $this->InvitationsUser->find('all', array(
			'conditions' => array(
				'user_id' => $userId
			)
		));
		$invites = Set::extract('/InvitationsUser/invitation_id', $habtm);
		$invitations = $this->find('all', array(
			'fields' => array(
				'id'
			),
			'conditions' => array(
				'or' => array(
					'user_id' => $userId,
					'id' => $invites
				)
			)
		));
		return Set::extract('/Invitation/id', $invitations);
	}
}
