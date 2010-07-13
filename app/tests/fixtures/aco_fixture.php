<?php
/* Aco Fixture generated on: 2010-07-13 13:07:56 : 1279053176 */
class AcoFixture extends CakeTestFixture {
	var $name = 'Aco';
	var $import = array('records' => true);

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'model' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'alias' => array('type' => 'string', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'ACOS_LFT_RGHT' => array('column' => array('lft', 'rght'), 'unique' => 0), 'ACOS_ALIAS' => array('column' => 'alias', 'unique' => 0), 'ACOS_MODEL_FOREIGN_KEY' => array('column' => array('foreign_key', 'model'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

}
?>