<?php
/**
 * Answer model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * Answer model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class Answer extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Answer';

/**
 * Validation rules
 *
 * @var array
 */
	var $validate = array(
		'description' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => 'Please fill in the required field.'
		)
	);

/**
 * BelongsTo association link
 *
 * @var array
 */
	var $belongsTo = array(
		'Roster' => array(
			'className' => 'Roster',
			'foreignKey' => 'roster_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Question' => array(
			'className' => 'Question',
			'foreignKey' => 'question_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
