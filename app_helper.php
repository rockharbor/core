<?php
/**
 * App helper class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app
 */

/**
 * App Helper
 *
 * @package       core
 * @subpackage    core.app
 */
class AppHelper extends Helper {

/**
 * Transforms a recordset from a hasAndBelongsToMany association to a list of selected
 * options for a multiple select element
 *
 * Overridden to allow deep form relations, like `HouseholdMember.0.Profile.first_name`
 * without errors because it is assumed to be a HABTM select field
 *
 * @param mixed $data
 * @param string $key
 * @return array
 * @access private
 */
	public function __selectedArray($data, $key = 'id') {
		if (!is_array($data)) {
			$model = $data;
			if (!empty($this->data[$model][$model])) {
				return $this->data[$model][$model];
			}
			if (!empty($this->data[$model])) {
				$data = $this->data[$model];
			}
		}
		$array = array();
		if (!empty($data)) {
			foreach ($data as $var) {
				if (isset($var[$key])) {
					$array[$var[$key]] = $var[$key];
				}
			}
		}
		return $array;
	}
}
?>
