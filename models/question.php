<?php
/**
 * Question model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Question model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Question extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	public $name = 'Question';

/**
 * BelongsTo association link
 *
 * @var array
 */
	public $belongsTo = array(
		'Involvement' => array(
			'className' => 'Involvement',
			'foreignKey' => 'involvement_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'description' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => 'Please fill in the required field.'
		)
	);

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	public $actsAs = array(
		'Ordered' => array(
			'field' => 'order',
			'foreign_key' => 'involvement_id'
		)
	);

/**
 * Sanitization rules
 *
 * @var array
 * @see Sanitizer.SanitizeBehavior
 */
	public $sanitize = array(
		'description' => 'stripScripts'
	);

}
