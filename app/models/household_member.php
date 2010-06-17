<?php
/**
 * Household member model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * HouseholdMember model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class HouseholdMember extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'HouseholdMember';

/**
 * BelongsTo association link
 *
 * @var array
 */
	var $belongsTo = array(
		'Household',
		'User'
	);

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	var $actsAs = array(
		'Linkable.AdvancedLinkable'
	);

}
?>