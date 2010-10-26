<?php
/**
 * MySQL layer for DBO
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models.datasources.dbo
 */

/**
 * Includes
 */
App::import('DataSource', 'dbo'.DS.'dbo_mysql');

/**
 * DboCoreMysql
 *
 * Extends CakePHP DboMysqlBase to support FULLTEXT indexes in schemas
 *
 * @package       core
 * @subpackage    core.app.models.datasources.dbo
 */
class DboCoreMysql extends DboMysql {

/**
 * Returns an array of the indexes in given datasource name.
 *
 * @param string $model Name of model to inspect
 * @return array Fields in table. Keys are column and unique
 */
	function index($model) {
		$index = array();
		$table = $this->fullTableName($model);
		if ($table) {
			$indexes = $this->query('SHOW INDEX FROM ' . $table);
			if (isset($indexes[0]['STATISTICS'])) {
				$keys = Set::extract($indexes, '{n}.STATISTICS');
			} else {
				$keys = Set::extract($indexes, '{n}.0');
			}
			foreach ($keys as $i => $key) {
				if (!isset($index[$key['Key_name']])) {
					$col = array();
					$index[$key['Key_name']]['column'] = $key['Column_name'];
					$index[$key['Key_name']]['unique'] = intval($key['Non_unique'] == 0);
					if ($key['Index_type'] == 'FULLTEXT') {
						$index[$key['Key_name']]['type'] = strtolower($key['Index_type']);
					}
				} else {
					if (!is_array($index[$key['Key_name']]['column'])) {
						$col[] = $index[$key['Key_name']]['column'];
					}
					$col[] = $key['Column_name'];
					$index[$key['Key_name']]['column'] = $col;
				}
			}
		}
		return $index;
	}

/**
 * Generate MySQL index alteration statements for a table.
 *
 * @param string $table Table to alter indexes for
 * @param array $new Indexes to add and drop
 * @return array Index alteration statements
 */
	function _alterIndexes($table, $indexes) {
		$alter = array();
		if (isset($indexes['drop'])) {
			foreach($indexes['drop'] as $name => $value) {
				$out = 'DROP ';
				if ($name == 'PRIMARY') {
					$out .= 'PRIMARY KEY';
				} else {
					$out .= 'KEY ' . $name;
				}
				$alter[] = $out;
			}
		}
		if (isset($indexes['add'])) {
			$add = $this->buildIndex($indexes['add']);
			foreach ($add as $built) {
				$alter[] = 'ADD '.$built;
			}
		}
		return $alter;
	}
}