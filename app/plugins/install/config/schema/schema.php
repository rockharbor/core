<?php
/* SVN FILE: $Id$ */
/* App schema generated on: 2011-03-15 11:03:54 : 1300215234*/
class AppSchema extends CakeSchema {
	var $name = 'App';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $acos = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'model' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'alias' => array('type' => 'string', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'acos_lft_rght' => array('column' => array('lft', 'rght'), 'unique' => 0), 'acos_alias' => array('column' => 'alias', 'unique' => 0), 'acos_modek_fk' => array('column' => array('foreign_key', 'model'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $addresses = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'address_line_1' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'address_line_2' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'city' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'state' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 2),
		'zip' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'lat' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '10,7'),
		'lng' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '10,7'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'foreign_key' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'model' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 32),
		'primary' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'model_key' => array('column' => array('foreign_key', 'model'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $alerts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 2500),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'group_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'expires' => array('type' => 'date', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'group_key' => array('column' => 'group_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $alerts_users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'alert_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'join' => array('column' => array('alert_id', 'user_id'), 'unique' => 1), 'alert_key' => array('column' => 'alert_id', 'unique' => 0), 'user_key' => array('column' => 'user_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $answers = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'roster_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'question_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 250),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'roster_key' => array('column' => 'roster_id', 'unique' => 0), 'question_key' => array('column' => 'question_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $api_classes = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary'),
		'api_package_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 36, 'key' => 'index'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 200),
		'slug' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 200),
		'file_name' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'method_index' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'property_index' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'flags' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 5),
		'coverage_cache' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '4,4'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'api_package_id' => array('column' => 'api_package_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $api_packages = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary'),
		'parent_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 36, 'key' => 'index'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'slug' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'parent_id' => array('column' => 'parent_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $app_settings = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'description' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'value' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1000),
		'type' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 45),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $aros = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'model' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'alias' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'aros_lft_rght' => array('column' => array('rght', 'lft'), 'unique' => 0), 'aros_model_fk' => array('column' => array('foreign_key', 'model'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $aros_acos = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'aro_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'aco_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'_create' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2),
		'_read' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2),
		'_update' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2),
		'_delete' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'aro_aco_key' => array('column' => array('aro_id', 'aco_id'), 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $attachments = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'foreign_key' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'dirname' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'basename' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'checksum' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'alternative' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'group' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'approved' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'model_key' => array('column' => array('model', 'foreign_key'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $campuses = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1000),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $campuses_revs = array(
		'version_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'version_created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1000),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'version_id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $classifications = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $comments = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'group_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'comment' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 2500),
		'created_by' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'user_key' => array('column' => 'user_id', 'unique' => 0), 'group_key' => array('column' => 'group_id', 'unique' => 0), 'creator_key' => array('column' => 'created_by', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $dates = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'start_date' => array('type' => 'date', 'null' => true, 'default' => NULL),
		'end_date' => array('type' => 'date', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'start_time' => array('type' => 'time', 'null' => true, 'default' => NULL),
		'end_time' => array('type' => 'time', 'null' => true, 'default' => NULL),
		'all_day' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'permanent' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'recurring' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'recurrance_type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10),
		'frequency' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 2),
		'weekday' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2),
		'day' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 2),
		'involvement_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'exemption' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'offset' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 2),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'involvement_key' => array('column' => 'involvement_id', 'unique' => 0), 'passed_key' => array('column' => array('end_date', 'end_time', 'permanent', 'involvement_id', 'exemption'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $errors = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary'),
		'level' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 15),
		'file' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 200),
		'line' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 5),
		'message' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $groups = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45, 'key' => 'unique'),
		'conditional' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 1),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'lft' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'rght' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'tree' => array('column' => array('lft', 'rght'), 'unique' => 1), 'name_key' => array('column' => 'name', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $household_members = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'household_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'confirmed' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'join' => array('column' => array('household_id', 'user_id'), 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $households = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'contact_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'contact_key' => array('column' => 'contact_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $id_linkages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'old_pk' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100, 'key' => 'index'),
		'old_table' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
		'new_model' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
		'new_pk' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'index_2' => array('column' => array('old_pk', 'old_table', 'new_model'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);
	var $involvement_types = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 32),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $involvements = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'ministry_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'involvement_type_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 64, 'key' => 'index'),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 2000),
		'roster_limit' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 3),
		'roster_visible' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'private' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'signup' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'take_payment' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'offer_childcare' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'force_payment' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'ministry_key' => array('column' => 'ministry_id', 'unique' => 0), 'involvement_type_key' => array('column' => 'involvement_type_id', 'unique' => 0), 'fulltext' => array('column' => array('name', 'description'), 'unique' => 0, 'type' => 'fulltext')),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $involvements_ministries = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'involvement_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'ministry_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'join' => array('column' => array('involvement_id', 'ministry_id'), 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $job_categories = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $leaders = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'model' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 32),
		'model_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'is_leader_key' => array('column' => array('user_id', 'model', 'model_id'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'model' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'model_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'action' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $merge_requests = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'model_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'merge_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'requester_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $ministries = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 64, 'key' => 'index'),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 2000),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'campus_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'private' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'tree' => array('column' => array('lft', 'rght'), 'unique' => 1), 'campus_key' => array('column' => 'campus_id', 'unique' => 0), 'fulltext' => array('column' => array('name', 'description'), 'unique' => 0, 'type' => 'fulltext')),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $ministries_revs = array(
		'version_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'version_created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 2000),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'campus_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'private' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'version_id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $notifications = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'read' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'type' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'body' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 500),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'user_key' => array('column' => 'user_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $payment_options = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'involvement_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 64),
		'total' => array('type' => 'float', 'null' => false, 'default' => NULL, 'length' => '7,2'),
		'deposit' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '7,2'),
		'childcare' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '7,2'),
		'account_code' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'tax_deductible' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'involvement_key' => array('column' => 'involvement_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $payment_types = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'type' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 2),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $payments = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'roster_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'amount' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => '7,2'),
		'payment_type_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'number' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'transaction_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'payment_placed_by' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'refunded' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'comment' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 2500),
		'involvement_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'belongsto_key' => array('column' => array('user_id', 'roster_id', 'payment_type_id', 'payment_placed_by'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $profiles = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'first_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'last_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'gender' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1),
		'birth_date' => array('type' => 'date', 'null' => true, 'default' => NULL),
		'adult' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'classification_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'marital_status' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1),
		'job_category_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'occupation' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'accepted_christ' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'accepted_christ_year' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 5),
		'baptism_date' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'allergies' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1000),
		'special_needs' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1000),
		'special_alert' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1000),
		'cell_phone' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10),
		'home_phone' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10),
		'work_phone' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10),
		'work_phone_ext' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10),
		'primary_email' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'alternate_email_1' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'alternate_email_2' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'cpr_certified_date' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'baby_dedication_date' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'qualified_leader' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'background_check_complete' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'background_check_by' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'background_check_date' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'grade' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2),
		'graduation_year' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 4),
		'created_by' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'created_by_type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'campus_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'email_on_notification' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'allow_sponsorage' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'household_contact_signups' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'elementary_school_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'middle_school_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'high_school_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'college_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'non_migratable' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'user_key' => array('column' => 'user_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $publications = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 64),
		'link' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $publications_users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'publication_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'join_key' => array('column' => array('publication_id', 'user_id'), 'unique' => 1), 'publication_key' => array('column' => 'publication_id', 'unique' => 0), 'user_key' => array('column' => 'user_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $questions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'involvement_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'order' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 2),
		'description' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 1000),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'involvement_key' => array('column' => 'involvement_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $queues = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary'),
		'to' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'cc' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'bcc' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'from' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'subject' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'delivery' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 4),
		'smtp_options' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'message' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'header' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $regions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $roles = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'ministry_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 200),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'ministry_key' => array('column' => 'ministry_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $roles_rosters = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'role_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'roster_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'join_key' => array('column' => array('role_id', 'roster_id'), 'unique' => 1), 'role_key' => array('column' => 'role_id', 'unique' => 0), 'roster_key' => array('column' => 'roster_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $rosters = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'involvement_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'payment_option_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'roster_status' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 2),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'involvement_key' => array('column' => 'involvement_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $schools = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 64),
		'type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 10),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'username' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 32, 'key' => 'index'),
		'password' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'last_logged_in' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'flagged' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'group_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'reset_password' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'login' => array('column' => array('username', 'password', 'active'), 'unique' => 0), 'fulltext' => array('column' => 'username', 'unique' => 0, 'type' => 'fulltext')),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $zipcodes = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'region_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'zip' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
}
?>