<?php
/**
 * Report component class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers.components
 */

/**
 * ReportComponent
 *
 * Aids in manipulating data for the use of reporting
 * 
 * @package       core
 * @subpackage    core.app.controllers.components
 */
class ReportComponent extends Object {

/**
 * Takes a list of POSTed `$fields` to export, and adds them to `$options` the field list(s)
 * 
 * @param type $fields Nested array of fields to export, as accepted by `ReportHelper`
 * @param type $options Search options (conditions, contains, etc)
 */
	function generateSearchOptions($fields = array(), $options = array()) {
		if (empty($fields)) {
			return $options;
		}
		$contained = isset($options['contain']) && $options['contain'] !== false ? $options['contain'] : array();
		$linked = isset($options['link']) ? $options['link'] : array();
		
		if (empty($linked) && empty($contained)) {
			$contained = $fields;
		}
		
		$contained = $this->_recursiveFieldSearch($fields, $contained);
		$linked = $this->_recursiveFieldSearch($fields, $linked);
		if (!empty($contained)) {
			$options['contain'] = $contained;
		}
		if (!empty($linked)) {
			$options['link'] = $linked;
		}
		return $options;
	}

/**
 * Adds fields to a `fields` key, and normalizes weird contain arrays
 * 
 * @param type $fields Nested array of fields to include
 * @param array $options Find options
 * @param boolean $add Add models that are not found in the `$options` array
 * @return array 
 */
	function _recursiveFieldSearch($fields = array(), $options = array()) {
		foreach ($options as $option => &$more) {
			if (is_integer($option)) {
				$options[$more] = array();
				unset($options[$option]);
				continue;
			}
			if (isset($fields[$option])) {
				if (is_array($more)) {
					if (!isset($more['fields'])) {
						$more['fields'] = array();
					}
					$possibleFields = array();
					foreach ($fields[$option] as $key => $val) {
						if (!is_array($val) && !is_integer($key)) {
							$possibleFields[] = $key;
						}
					}
					$more['fields'] = array_merge($more['fields'], $possibleFields);
					$more = $this->_recursiveFieldSearch($fields[$option], $more);
				}
			}
		}
		return $options;
	}
	
}