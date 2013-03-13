<?php
/**
 * Classification model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Classification model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Classification extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	public $name = 'Classification';

/**
 * Default order
 *
 * @var string
 */
	public $order = ':ALIAS:.name ASC';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'rule' => 'notempty',
			'message' => 'Please fill in the required field.'
		)
	);

}
