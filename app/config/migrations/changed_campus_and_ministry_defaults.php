<?php
class M4c72c00b70744bbd88180734b36fd16a extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 * @access public
 */
	public $description = '';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'campuses' => array(
					'active' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
				),
				'ministries' => array(
					'group_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
					'active' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
				),
			),
			'create_table' => array(
				'involvements_ministries' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
					'involvement_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
					'ministry_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM'),
				),
			),
			'create_field' => array(
				'users' => array(
					'reset_password' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
				),
			),
			'drop_table' => array(
				'api_classes', 'api_packages', 'campuses_revs', 'classifications', 'errors', 'involvements_revs', 'ministries_revs'
			),
		),
		'down' => array(
			'alter_field' => array(
				'campuses' => array(
					'active' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
				),
				'ministries' => array(
					'group_id' => array('type' => 'integer', 'null' => true, 'default' => '9', 'length' => 10),
					'active' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
				),
			),
			'drop_table' => array(
				'involvements_ministries'
			),
			'drop_field' => array(
				'users' => array('reset_password',),
			),
			'create_table' => array(
				'api_classes' => array(
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
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'api_package_id' => array('column' => 'api_package_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM'),
				),
				'api_packages' => array(
					'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary'),
					'parent_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 36, 'key' => 'index'),
					'name' => array('type' => 'string', 'null' => false, 'default' => NULL),
					'slug' => array('type' => 'string', 'null' => false, 'default' => NULL),
					'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL),
					'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'parent_id' => array('column' => 'parent_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM'),
				),
				'campuses_revs' => array(
					'version_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
					'version_created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
					'id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
					'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
					'description' => array('type' => 'text', 'null' => true, 'default' => NULL),
					'active' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'indexes' => array(
						'PRIMARY' => array('column' => 'version_id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM'),
				),
				'classifications' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
					'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 45),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM'),
				),
				'errors' => array(
					'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary'),
					'level' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 15),
					'file' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 200),
					'line' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 5),
					'message' => array('type' => 'text', 'null' => false, 'default' => NULL),
					'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM'),
				),
				'involvements_revs' => array(
					'version_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
					'version_created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
					'id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
					'ministry_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
					'involvement_type_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
					'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
					'description' => array('type' => 'text', 'null' => true, 'default' => NULL),
					'roster_limit' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 3),
					'roster_visible' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'group_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
					'signup' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'require_payment' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'offer_childcare' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'active' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'indexes' => array(
						'PRIMARY' => array('column' => 'version_id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM'),
				),
				'ministries_revs' => array(
					'version_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
					'version_created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
					'id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
					'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
					'description' => array('type' => 'text', 'null' => true, 'default' => NULL),
					'campus_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
					'group_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
					'active' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
					'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
					'indexes' => array(
						'PRIMARY' => array('column' => 'version_id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM'),
				),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function after($direction) {
		return true;
	}
}
?>