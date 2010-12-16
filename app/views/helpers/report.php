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
 * Array of normalized fields to use to create headers and to determine what
 * data to pull from results
 *
 * @var array
 */
	var $_fields = array();

/**
 * Array of headers set by ReportHelper::createHeaders()
 *
 * @var array
 */
	var $headers = array();

/**
 * Resets the ReportHelper so it can used again
 */
	function reset() {
		$this->_fields = array();
		$this->headers = array();
		$this->results = array();
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
			$this->_fields = $data = $this->normalize($data);
		}

		foreach ($data as $model => $fields) {
			if (is_array($fields)) {
				$this->createHeaders($fields);
			} else {
				$this->headers[] = Inflector::humanize($model);
			}
		}

		return $this->headers;
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