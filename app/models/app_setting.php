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
	var $name = 'AppSetting';

/**
 * Extra behaviors for this model
 *
 * @var array
 */
	var $actsAs = array('Logable');
}
?>