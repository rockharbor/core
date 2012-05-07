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
 * The active data
 * 
 * @var array 
 */
	var $data = array();
	
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
 * Array of squashed fields
 * 
 * {{{
 * array(
 *   field_to_use => array(
 *     'alias' => // header alias
 *     'fields' => // array of fields to squash
 *     'format' => // sprintf format
 *   )
 * );
 * }}}
 *
 * @var array
 * @access protected
 */
	var $_squashed = array();
	
/**
 * Array of fields that can contain multiple records
 * 
 * This is useful for relationships that may return an undetermined amount of
 * records, like hasMany records. When a "multiple" field is defined, the number
 * of records is pulled from the highest count of that path in the records
 *  
 * {{{
 * array(
 *   path => array(
 *     'max' => // maximum number of columns to expand to, or none for auto
 *     'expand' => // type of column expansion to use
 *   )
 * );
 * }}}
 * 
 * @var array
 * @see ReportHelper::multiple()
 */
	var $_multiples = array();
	
/**
 * Extra helpers needed for this helper
 * 
 * @var array
 */
	var $helpers = array(
		'Form'
	);

/**
 * Resets the ReportHelper so it can used again
 */
	function reset() {
		$this->_fields = array();
		$this->_aliases = array();
		$this->_squashed = array();
		$this->_multiple = array();
	}

/**
 * Sets active data
 * 
 * @param array $data Cake find results
 */
	function set($data = array()) {
		$this->data = $data;
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

		$paths = Set::flatten($this->_fields);
		$squashed = array_keys($this->_squashed);
		
		foreach ($this->_squashed as $squash) {
			foreach ($squash['fields'] as $field) {
				unset($paths[$field]);
			}
		}
		$paths = array_keys($paths);
		
		$allpaths = array_keys(Set::flatten($data));
		$flat = array_intersect($paths, $allpaths);
		$headers = array();
		
		foreach ($flat as $path) {
			$exp = explode('.', $path);
			$name = $exp[count($exp)-1];
			
			if (array_key_exists($path, $this->_multiples) && !empty($this->data)) {
				if (is_null($this->_multiples[$path]['max']) && $this->_multiples[$path]['expand'] == 'expand') {
					$max = 0;
					foreach ($this->data as $record) {
						$count = Set::extract('/'.implode('/', $exp), $record);
						if (count($count) > $max) {
							$max = count($count);
						}
					}
					$this->_multiples[$path]['max'] = $max;
				}
				
				switch ($this->_multiples[$path]['expand']) {
					case 'expand':
						for ($c = 0; $c < $this->_multiples[$path]['max']; $c++) {
							if (array_key_exists($path, $this->_aliases)) {
								$headers[] = $this->_aliases[$path].' '.($c+1);
							} else {
								$headers[] = Inflector::humanize($name).' '.($c+1);
							}
						}
					break;
					default:
						if (array_key_exists($path, $this->_aliases)) {
							$headers[] = $this->_aliases[$path];
						} else {
							$headers[] = Inflector::humanize($name);
						}
					break;
				}
				
				continue;
			}
			
			if (in_array($squashed, $this->_squashed)) {
				$headers[] = $this->_squashed[$squashed]['alias'];
			} elseif (array_key_exists($path, $this->_aliases)) {
				$headers[] = $this->_aliases[$path];
			} else {
				$headers[] = Inflector::humanize($name);
			}
		}

		return $headers;
	}

/**
 * Takes Cake data and pulls out just the information based on the headers. Useful
 * for HtmlHelper::tableCells() and similar functions.
 * 
 * Uses `$this->data` to generate rows. See `ReportHelper::set()`
 *
 * Note: `ReportHelper::createHeaders()` needs to be run before this function so
 * `ReportHelper::getResults()` knows what data to pull
 *
 * @return array An array of the data based on the headers
 */
	function getResults() {
		if (empty($this->_fields) || empty($this->data)) {
			return array();
		}
		$squashed = array_keys($this->_squashed);
		$paths = Set::flatten($this->_fields);
		foreach ($this->_squashed as $squash) {
			foreach ($squash['fields'] as $field) {
				unset($paths[$field]);
			}
		}
		$paths = array_keys($paths);
		$clean = array();
		foreach ($this->data as $rawrow) {
			$flat = Set::flatten($rawrow);
			$cleanrow = array();
			foreach ($paths as $path) {
				if (in_array($path, $squashed)) {
					$values = array();
					foreach ($this->_squashed[$path]['fields'] as $fpath) {
						if (array_key_exists($fpath, $flat)) {
							$values[] = $flat[$fpath];
						} else {
							$values[] = null;
						}
					}
					$params = array_merge(array($this->_squashed[$path]['format']), $values);
					$cleanrow[] = call_user_func_array('sprintf', $params);
				} elseif (array_key_exists($path, $flat)) {
					$cleanrow[] = $flat[$path];
				} elseif (array_key_exists($path, $this->_multiples)) {
					$records = Set::extract('/'.str_replace('.', '/', $path), $rawrow);
					switch ($this->_multiples[$path]['expand']) {
						case 'expand':
							for ($c = 0; $c < $this->_multiples[$path]['max']; $c++) {
								if (isset($records[$c])) {
									$cleanrow[] = $records[$c];
								} else {
									$cleanrow[] = null;
								}
							}
						break;
						case 'concat':
							$cleanrow[] = implode(', ', $records);
						break;
						default:
							$cleanrow[] = $records[0];
						break;
					}
				} else {
					$cleanrow[] = null;
				}
			}
			$clean[] = $cleanrow;
		}
		return $clean;
	}

/**
 * Marks a field (i.e., User.Roster.Involvement.name) as having multiple records
 * per single record
 * 
 * ### Column Expansion (`$expand`):
 * - 'expand' : Count the number of records for a multi-record result and create
 * columns for each record (checks the first record)
 * - 'concat' : Concatenate the row values into a single column (comma-delimited)
 * - 'none' : Just use the first record's value
 * 
 * The `max` key is used internally when generating headers and row results.
 * 
 * @param string $path Field dot-path
 * @param boolean $expand Column expansion type (see options above)
 */
	function multiple($path = null, $expand = 'none') {
		$this->_multiples[$path] = array(
			'expand' => $expand,
			'max' => null
		);
	}
	
/**
 * Gets or sets multiple record models. If setting, make sure they are set _before_
 * `createHeaders()` is called to ensure they make it into the headers
 * 
 * @param string $str The models that can have multiple records
 */
	function multipleRecords($str = '') {
		if (!empty($str)) {
			$this->_multiples = unserialize($str);
		} else {
			return serialize($this->_multiples);
		}
	}
	
/**
 * Squashes fields into a single field
 * 
 * Multiple fields can be 'squashed' into a single field. This is useful for things
 * like addresses. The `$squashee` is the field that will be overwritten by the
 * squashed `$fields`. `$format` is a `sprintf`-type formatting, and `$alias` is
 * the header alias.
 * 
 * @param string $squashee The field to squash
 * @param array $fields The fields to be squashed
 * @param string $format How to format the fields (when they are data values)
 * @param string $alias The header alias
 */
	function squash($squashee = '', $fields = array(), $format = null, $alias = '') {
		$this->_squashed[$squashee] = compact('fields', 'format', 'alias');
	}
	
/**
 * Gets or sets squashed fields. If setting, make sure they are set _before_
 * `createHeaders()` is called to ensure they make it into the headers
 * 
 * @param string $str The fields to sqhash
 */
	function squashFields($str = '') {
		if (!empty($str)) {
			$this->_squashed = unserialize($str);
		} else {
			return serialize($this->_squashed);
		}
	}
	
/**
 * Addes necessary hidden fields for export at the end of a report form
 * 
 * @param string $model The data model
 * @return string 
 */
	function end($model = 'Export') {
		$out = '';
		$out .= $this->Form->hidden("$model.header_aliases", array('value' => $this->headerAliases()));
		$out .= $this->Form->hidden("$model.squashed_fields", array('value' => $this->squashFields()));
		$out .= $this->Form->hidden("$model.multiple_records", array('value' => $this->multipleRecords()));
		foreach ($this->_squashed as $squashed) {
			foreach ($squashed['fields'] as $field) {
				$out .= $this->Form->hidden("$model.$field", array('value' => 1));
			}
		}
		return $out;
	}
}