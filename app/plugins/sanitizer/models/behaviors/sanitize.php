<?php
/**
 * Sanitize behavior class
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       sanitizer
 * @subpackage    sanitizer.models.behaviors
 */

/**
 * Includes
 */
App::import('Core', 'Sanitize');

/**
 * Sanitize behavior
 *
 * Sanitizes inputs automatically before saving them to the database. By default,
 * uses Sanitize::clean() and strips html. To use a different method on the
 * Sanitize class, set the $sanitize var with the key as the field name and the
 * value the method. Optionally pass an array with the options that you would
 * normally pass to Sanitize.
 *
 * {{{
 * // clean the name field using Sanitize::html()
 * var $sanitize = array(
 *   'name' => 'html'
 * );
 *
 * // or clean the name field using Sanitize::paranoid() and allowing '%'
 * var $sanitize = array(
 *   'name' => array(
 *     'paranoid => array('%')
 *   )
 * );
 * }}}
 *
 * @package       sanitizer
 * @subpackage    sanitizer.models.behaviors
 * @todo allow defining the generic santization rule
 * @todo allow multiple rules per field (like validation)
 */
class SanitizeBehavior extends ModelBehavior {

/**
 * Settings keyed to the model
 * 
 * ### Settings:
 * - `validate` Should the model validate before or after the behavior?
 *		- 'before' Behavior sanitizes after the validation
 *		- 'after' Behavior sanitizes before the validation
 * 
 * @var array
 */
	var $settings = array();

/**
 * Methods allowed to be called in lieu of the generic one, Sanitize::clean()
 *
 * @var array
 */
	var $_methods = array(
		'clean',
		'html',
		'paranoid',
		'stripAll',
		'stripImages',
		'stropScripts',
		'stripWhitespace',
	);

/**
 * Setup the behavior
 *
 * @param Model $Model The calling model
 * @param array $settings Settings
 */
	function setup(&$Model, $settings = array()) {
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = array('validate' => 'after');
		}
		if (!is_array($settings)) {
			$settings = array();
		}
		$this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], $settings);
	}

/**
 * If `'validate' => 'after'` data validation will occur after sanitization
 *
 * @param Model $Model The calling model
 * @return boolean True
 */
	function beforeValidate(&$Model) {
		if ($this->settings[$Model->alias]['validate'] == 'before') {
			return true;
		}
		$this->_sanitize($Model);
		return true;
	}

/**
 * If `'validate' => 'before'` data validation will occur before sanitization
 *
 * @param Model $Model The calling model
 * @return boolean True
 */
	function beforeSave(&$Model) {
		if ($this->settings[$Model->alias]['validate'] == 'after') {
			return true;
		}
		$this->_sanitize($Model);
		return true;
	}

/**
 * Sanitizes data. By default, uses `Sanitize::clean()` and removes html. Define
 * custom sanitization rules on a per-field basis using the `$sanitize` var
 * within the model
 *
 * @param Model $Model The calling model
 */
	function _sanitize($Model) {
		$sanitize = isset($Model->sanitize) ? $Model->sanitize : array();
		if ($sanitize === false) {
			return;
		}
		foreach ($Model->data[$Model->alias] as $field => &$value) {
			$method = null;
			if (isset($sanitize[$field])) {
				$method = $sanitize[$field];
				if (is_array($sanitize[$field])) {
					$method = key($sanitize[$field]);
					$options = array($sanitize[$field][$method]);
				}
			}
			if ($method === false) {
				continue;
			}
			if (in_array($method, $this->_methods)) {
				$args = isset($options) ? $options : array();
				array_unshift($args, $value);
				$value = call_user_func_array(array('Sanitize', $method), $args);
			} else {
				$value = Sanitize::clean($value, array(
					'remove_html' => true
				));
			}
		}		
	}

}

?>