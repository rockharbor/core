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
	var $order = 'Group.parent_id ASC';

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
 * throws an error when it doesn't exist, so since it needs to it might as
 * well work properly just in case we decide to let someone edit groups from
 * the app.
 *
 * @return mixed The parent, or null if none
 * @access public
 */
	function parentNode() {
		if (!$this->id && empty($this->data)) {
			return null;
		}
		$data = $this->data;
		if (empty($this->data)) {
			$data = $this->read();
		}
		if (empty($data['Group']['parent_id'])) {
			return null;
		} else {
			return $data['Group']['parent_id'];
		}
	}
}
?>