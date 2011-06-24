<?php
/**
 * Job category model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * JobCategory model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class JobCategory extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'JobCategory';

/**
 * Validation rules
 *
 * @var array
 */
	var $validate = array(
		'name' => array(
			'rule' => 'notempty',
			'message' => 'Please fill in the required field.'
		)
	);

}
?>