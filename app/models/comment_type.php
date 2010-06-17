<?php
/**
 * Comment type model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * CommentType model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class CommentType extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'CommentType';

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
		'Comments'
	);

/**
 * BelongsTo association link
 *
 * @var array
 */
	var $belongsTo = array(
		'Group'
	);

}
?>