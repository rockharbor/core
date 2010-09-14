<?php

class Records {

/**
 * Inserts multiple recrods into the database
 *
 * @return boolean
 */
	function insert() {
		$db =& ConnectionManager::getDataSource('default');
		$table = Inflector::pluralize(Inflector::underscore($this->name));

		if (isset($this->records) && !empty($this->records)) {
			foreach ($this->records as $record) {
				$record['created'] = date('Y-m-d H:i:s');
				$record['modified'] = date('Y-m-d H:i:s');
				$fields = array_keys($record);
				$values[] = '(' . implode(', ', array_map(array(&$db, 'value'), array_values($record))) . ')';
			}
			return $db->insertMulti($table, $fields, $values);
		}
		return true;
	}


}

?>
