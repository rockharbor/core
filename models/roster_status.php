<?php
/**
 * Roster Status model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * RosterStatus model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class RosterStatus extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'RosterStatus';

/**
 * Default order
 *
 * @var string
 */
	var $order = ':ALIAS:.name ASC';

/**
 * HasMany relationship
 *
 * This is here only because it's needed by containable and linkable
 *
 * @var array
 */
	var $hasMany = array(
		'Roster'
	);
}
?>