<?php
/**
 * Select options helper class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.views.helpers
 */

/**
 * SelectOptions Helper
 *
 * Central place for common lists to be stored, as well
 * as common mappings. Also includes functions aiding
 * in creating lists, or list logic.
 *
 * @package       core
 * @subpackage    core.app.views.helpers
 */
class SelectOptionsHelper extends AppHelper {

/**
 * List of age groups
 *
 * @var array
 * @access public
 */
	var $ageGroups = array(
		'0-2.99'=>'0-2',
		'3-5.99'=>'3-5',
		'6-11.99'=>'6-11',
		'12-14.99'=>'12-14',
		'15-17.99'=>'15-17',
		'18-25.99'=>'18-25',
		'26-35.99'=>'26-35',
		'36-45.99'=>'36-45',
		'46-55.99'=>'46-55',
		'56-65.99'=>'56-65',
		'66-1000'=>'66+'
	);

/**
 * List of states
 *
 * @var array
 * @access public
 */
	var $states = array(
		'AL'=>"Alabama",
		'AK'=>"Alaska",
		'AZ'=>"Arizona",
		'AR'=>"Arkansas",
		'CA'=>"California",
		'CO'=>"Colorado",
		'CT'=>"Connecticut",
		'DE'=>"Delaware",
		'DC'=>"District Of Columbia",
		'FL'=>"Florida",
		'GA'=>"Georgia",
		'HI'=>"Hawaii",
		'ID'=>"Idaho",
		'IL'=>"Illinois",
		'IN'=>"Indiana",
		'IA'=>"Iowa",
		'KS'=>"Kansas",
		'KY'=>"Kentucky",
		'LA'=>"Louisiana",
		'ME'=>"Maine",
		'MD'=>"Maryland",
		'MA'=>"Massachusetts",
		'MI'=>"Michigan",
		'MN'=>"Minnesota",
		'MS'=>"Mississippi",
		'MO'=>"Missouri",
		'MT'=>"Montana",
		'NE'=>"Nebraska",
		'NV'=>"Nevada",
		'NH'=>"New Hampshire",
		'NJ'=>"New Jersey",
		'NM'=>"New Mexico",
		'NY'=>"New York",
		'NC'=>"North Carolina",
		'ND'=>"North Dakota",
		'OH'=>"Ohio",
		'OK'=>"Oklahoma",
		'OR'=>"Oregon",
		'PA'=>"Pennsylvania",
		'RI'=>"Rhode Island",
		'SC'=>"South Carolina",
		'SD'=>"South Dakota",
		'TN'=>"Tennessee",
		'TX'=>"Texas",
		'UT'=>"Utah",
		'VT'=>"Vermont",
		'VA'=>"Virginia",
		'WA'=>"Washington",
		'WV'=>"West Virginia",
		'WI'=>"Wisconsin",
		'WY'=>"Wyoming"
	);

/**
 * Map for genders
 *
 * @var array
 * @access public
 */
	var $genders = array(
		null => 'Unknown',
		'm' => 'Male',
		'f' => 'Female'
	);

/**
 * Map for marital statuses
 *
 * @var array
 * @access public
 */
	var $maritalStatuses = array(
		null => 'Unknown',
		's' => 'Single',
		'm' => 'Married',
		'w' => 'Widowed',
		'd' => 'Divorced'
	);

/**
 * Map for grades
 *
 * @var array
 * @access public
 */
	var $grades = array(
		null => 'Unknown',
		-1 => 'Pre-kinder',
		0 => 'Kindergarten',
		1 => '1st',
		2 => '2nd',
		3 => '3rd',
		4 => '4th',
		5 => '5th',
		6 => '6th',
		7 => '7th',
		8 => '8th',
		9 => '9th',
		10 => '10th',
		11 => '11th',
		12 => '12th'
	);

/**
 * Map for boolean answers
 *
 * @var array
 * @access public
 */
	var $booleans = array(
		null => 'Unknown',
		0 => 'No',
		1 => 'Yes'
	);

/**
 * Magic method to allow calling singular function names to get the value from
 * the pluralized array
 *
 * @param string $name The function name
 * @param array $arguments Should only have 1 value, the string to map
 * @return string Mapped string
 */
	function __call($name, $arguments) {
		if (isset($this->{Inflector::pluralize($name)})) {
			return $this->{Inflector::pluralize($name)}[$arguments[0]];
		}
	}

/**
 * Returns the data value provided by viewVars, similar to how
 * FormHelper::input() auto-fills the values with `$this->data`. Returns a
 * non-breaking space if the index is missing from the viewVars array, or the
 * value if the viewVars array is missing altogether
 *
 * Helps prevent Undefined Index errors when trying things like
 * `$jobCategories[$profile['Profile']['job_category_id']]` and the user has no
 * job category. Instead, use
 * `$this->SelectOptions->list('Profile.job_category_id', $profile)`
 *
 * @param string $modelField The model field string, separated with a '.' like
 *		how FormHelper::input() accepts
 * @param array $data The data to use
 * @param mixed $return What to return on missing/empty values
 * @return string
 */
	function value($modelField, $data = array(), $return = '&nbsp;') {
		list($model, $field) = explode('.', $modelField);
		if (isset($data[$model]) && isset($data[$model][$field])) {
			$View = ClassRegistry::getObject('view');
			$name = Inflector::variable(
				Inflector::pluralize(preg_replace('/_id$/', '', $field))
			);
			if (isset($View->viewVars[$name])) {
				if (isset($View->viewVars[$name][$data[$model][$field]])) {
					$return = $View->viewVars[$name][$data[$model][$field]];
				}
			} else {
				$return = $data[$model][$field];
			}
		}
		return $return;
	}

/**
 * Generates option lists for common <select /> menus.
 * Taken from FormHelper, made public here, includes some changes.
 *
 * @param string $name Type of list to generate
 * @param array $options Options specific to name
 * @return array The key=>value pairs
 * @access public
 */
	function generateOptions($name, $options = array()) {
		$_defaultOptions = array(
			'order' => '',
			'monthNames' => true
		);

		$options = array_merge($_defaultOptions, $options);

		if (!empty($this->options[$name])) {
			return $this->options[$name];
		}

		$data = array();

		switch ($name) {
			case 'minute':
				if (isset($options['interval'])) {
					$interval = $options['interval'];
				} else {
					$interval = 1;
				}
				$i = 0;
				while ($i < 60) {
					$data[sprintf('%02d', $i)] = sprintf('%02d', $i);
					$i += $interval;
				}
			break;
			case 'hour':
				for ($i = 1; $i <= 12; $i++) {
					$data[sprintf('%02d', $i)] = $i;
				}
			break;
			case 'hour24':
				for ($i = 0; $i <= 23; $i++) {
					$data[sprintf('%02d', $i)] = $i;
				}
			break;
			case 'meridian':
				$data = array('am' => 'am', 'pm' => 'pm');
			break;
			case 'day':
				$min = 1;
				$max = 31;

				if (isset($options['min'])) {
					$min = $options['min'];
				}
				if (isset($options['max'])) {
					$max = $options['max'];
				}

				for ($i = $min; $i <= $max; $i++) {
					$data[sprintf('%02d', $i)] = $i;
				}
			break;
			case 'week':
				$data['0'] = __('Sunday', true);
				$data['1'] = __('Monday', true);
				$data['2'] = __('Tuesday', true);
				$data['3'] = __('Wednesday', true);
				$data['4'] = __('Thursday', true);
				$data['5'] = __('Friday', true);
				$data['6'] = __('Saturday', true);
			break;
			case 'month':
				if ($options['monthNames'] === true) {
					$data['01'] = __('January', true);
					$data['02'] = __('February', true);
					$data['03'] = __('March', true);
					$data['04'] = __('April', true);
					$data['05'] = __('May', true);
					$data['06'] = __('June', true);
					$data['07'] = __('July', true);
					$data['08'] = __('August', true);
					$data['09'] = __('September', true);
					$data['10'] = __('October', true);
					$data['11'] = __('November', true);
					$data['12'] = __('December', true);
				} else if (is_array($options['monthNames'])) {
					$data = $options['monthNames'];
				} else {
					for ($m = 1; $m <= 12; $m++) {
						$data[sprintf("%02s", $m)] = strftime("%m", mktime(1, 1, 1, $m, 1, 1999));
					}
				}
			break;
			case 'year':
				$current = intval(date('Y'));

				if (!isset($options['min'])) {
					$min = $current - 20;
				} else {
					$min = $options['min'];
				}

				if (!isset($options['max'])) {
					$max = $current + 20;
				} else {
					$max = $options['max'];
				}
				if ($min > $max) {
					list($min, $max) = array($max, $min);
				}
				for ($i = $min; $i <= $max; $i++) {
					$data[$i] = $i;
				}
				if ($options['order'] != 'asc') {
					$data = array_reverse($data, true);
				}
			break;
		}
		$this->__options[$name] = $data;
		return $this->__options[$name];
	}

}

