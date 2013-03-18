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
	public $name = 'HouseholdMember';

/**
 * BelongsTo association link
 *
 * @var array
 */
	public $belongsTo = array(
		'Household',
		'User'
	);

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	public $actsAs = array(
		'Linkable.AdvancedLinkable',
		'Containable'
	);

}
