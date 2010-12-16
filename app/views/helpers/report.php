<?php
/**
 * Report helper class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.views.helpers
 */

/**
 * Report Helper
 *
 * Aids in setting up exported report data
 *
 * @package       core
 * @subpackage    core.app.views.helpers
 */
class ReportHelper extends AppHelper {

/**
 * List of field name aliases so printed headers are the same as field labels
 *
 * @var array
 * @access protected
 */
	var $_aliases = array();

/**
 * Array of normalized fields to use to create headers and to determine what
 * data to pull from results
 *
 * @var array
 * @access protected
 */
	var $_fields = array();

/**
 * Resets the ReportHelper so it can used again
 */
	function reset() {
		$this->_fields = array();
		$this->_aliases = array();
	}

/**
 * Stores an alias. Gets aliases if no argument is passed. 
 *
 * {{{
 * $this->Report->alias(array('User.Profile.birth_date' => 'DOB'));
 * $this->Report->alias('User.Profile.birth_date', 'DOB');
 * }}}
 *
 * @param mixed $field An array with the field=>alias, or a field string
 * @param string $alias Alias string
 * @return array
 */
	function alias($field = array(), $alias = '') {
		if (empty($field)) {
			return $this->_aliases;
		}
		if (is_array($field)) {
			$alias = $field[key($field)];
			$field = key($field);
		}
		$this->_aliases[$field] = $alias;
	}

/**
 * Sets/gets aliases as a serialized string to pass through a form
 *
 * @param string $str Serialized array. Blank to get
 */
	function headerAliases($str = '') {
		if (!empty($str)) {
			$this->_aliases = unserialize($str);
		} else {
			return serialize($this->_aliases);
		}
	}

/**
 * Takes Cake data array and pulls out any unchecked fields
 *
 * @param array $data Cake formated POST data
 * @return array
 */
	function normalize($data) {
		foreach ($data as $model => &$fields) {
			if (is_array($fields)) {
				$fields = $this->normalize($fields);
			}
			if ($fields === 0 || $fields === '0') {
				unset($data[$model]);
			}
		}
		return $data;
	}

/**
 * Creates header array from recursive list of models => fields sent by a form
 * consisting of checkboxes. Checks for unchecked boxes.
 *
 * @param array $data The models-field keys
 * @return array Headers
 */
	function createHeaders($data) {
		if (empty($this->_fields)) {
			$this->_fields = $this->normalize($data);
		}

		$paths = array_keys(Set::flatten($this->_fields));
		$allpaths = array_keys(Set::flatten($data));
		$flat = array_intersect($paths, $allpaths);
		$headers = array();

		foreach ($flat as $path) {
			$exp = explode('.', $path);
			$name = $exp[count($exp)-1];
			if (array_key_exists($path, $this->_aliases)) {
				$headers[] = $this->_aliases[$path];
			} else {
				$headers[] = Inflector::humanize($name);
			}
		}

		return $headers;
	}

/**
 * Takes Cake data and pulls out just the information based on the headers. Useful
 * for HtmlHelper::tableCells() and similar functions
 *
 * Note: `ReportHelper::createHeaders()` needs to be run before this function so
 * `ReportHelper::getResults()` knows what data to pull
 *
 * @param array $raw Data as given by a Cake find
 * @return array An array of the data based on the headers
 */
	function getResults($raw = array()) {
		if (empty($this->_fields) || empty($raw)) {
			return array();
		}
		$paths = array_keys(Set::flatten($this->_fields));
		$clean = array();
		foreach ($raw as $rawrow) {
			$flat = Set::flatten($rawrow);
			$cleanrow = array();
			foreach ($paths as $path) {
				if (array_key_exists($path, $flat)) {
					$cleanrow[] = $flat[$path];
				} else {
					$cleanrow[] = null;
				}
			}
			$clean[] = $cleanrow;
		}
		return $clean;
	}
}