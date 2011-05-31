<?php
/* Aco Fixture generated on: 2010-07-13 13:07:56 : 1279053176 */
class AcoFixture extends CakeTestFixture {
	var $name = 'Aco';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'model' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'alias' => array('type' => 'string', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 8),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'acos_lft_rght' => array('column' => array('lft', 'rght'), 'unique' => 0), 'acos_alias' => array('column' => 'alias', 'unique' => 0), 'acos_modek_fk' => array('column' => array('foreign_key', 'model'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	
	var $records = array(
		array(
			'id' => 1,
			'parent_id' => null,
			'model' => null,
			'foreign_key' => null,
			'alias' => 'controllers',
			'lft' => 1,
			'rght' => 2
		)
	);

}
?>