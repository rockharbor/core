<?php
/**
 * Roster status model class.
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
 * @todo Move into a variable in the Roster model
 */
class RosterStatus extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'RosterStatus';

/**
 * Validation rules
 *
 * @var array
 */
	var $validate = array(
		'name' => 'notempty'
	);

/**
 * HasMany association link
 *
 * @var array
 */
	var $hasMany = array(
		'Roster'
	);

}
?>