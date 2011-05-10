<?php
/**
 * Region model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Region model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Region extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Region';

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
		'Zipcode' => array(
			'dependent' => true
		)
	);

}
?>