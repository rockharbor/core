<?php
/**
 * Publication model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Publication model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Publication extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Publication';

/**
 * Validation rules
 *
 * @var array
 */
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please fill in the required field.'
			)
		),
		'description' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please fill in the required field.'
			)
		)
	);

/**
 * HasAndBelongsToMany association link
 *
 * @var array
 */
	var $hasAndBelongsToMany = array(
		'User'
	);
}
?>