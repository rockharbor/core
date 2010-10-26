<?php
/**
 * Core's Dbo Source
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.models.datasources
 */

/**
 * Includes
 */
App::import('Core', array('Set', 'String'));
App::import('DataSource', 'DboSource');

/**
 * CoreDboSource
 *
 * Extends CakePHP DboSource to support FULLTEXT indexes in schemas
 *
 * @package       core
 * @subpackage    core.app.models.datasources
 */
class CoreDboSource extends DboSource {

/**
 * Format indexes for create table
 *
 * @param array $indexes
 * @param string $table
 * @return array
 * @access public
 */
	function buildIndex($indexes, $table = null) {
		$join = array();
		foreach ($indexes as $name => $value) {
			$out = '';
			$type = 'KEY';
			if ($name == 'PRIMARY') {
				$out .= 'PRIMARY ';
				$name = null;
			} else {
				if (!empty($value['unique'])) {
					$out .= 'UNIQUE ';
				} elseif (isset($value['type'])) {
					$out .= strtoupper($value['type']).' ';
					$type = 'INDEX';
				}
				$name = $this->startQuote . $name . $this->endQuote;
			}
			if (is_array($value['column'])) {
				$out .= $type . ' ' . $name . ' (' . implode(', ', array_map(array(&$this, 'name'), $value['column'])) . ')';
			} else {
				$out .= $type . ' ' . $name . ' (' . $this->name($value['column']) . ')';
			}
			$join[] = $out;
		}
		return $join;
	}
}