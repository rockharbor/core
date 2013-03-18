<?php
/**
 * AppSetting model class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models
 */

/**
 * AppSetting model
 *
 * @package       core
 * @subpackage    core.app.models
 */
class AppSetting extends AppModel {

/**
 * The name of the model
 *
 * @var string
 */
	public $name = 'AppSetting';

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	public $actsAs = array(
		'Cacher.Cache' => array(
			'duration' => '+1 year',
			'clearOnSave' => true,
			'clearOnDelete' => true,
			'auto' => true
		),
		'Logable'
	);

/**
 * Recursive setting
 *
 * @var integer
 */
	public $recursive = 0;

/**
 * HasOne association link
 *
 * @var array
 */
	public $hasOne = array(
		'Image' => array(
			'className' => 'Image',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array('Image.model' => 'AppSetting', 'Image.group' => 'Image')
		)
	);

/**
 * Don't use Sanitizer.Sanitize behavior
 *
 * @var mixed
 */
	public $sanitize = false;
}
