<?php
/**
 * School model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * School model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class School extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'School';

/**
 * Types of schools
 *
 * @var array
 */
	var $types = array(
		'e' => 'Elementary School',
		'm' => 'Middle School',
		'h' => 'High School',
		'c' => 'College'
	);

}
?>