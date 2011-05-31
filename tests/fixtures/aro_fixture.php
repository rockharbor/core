<?php
/* Aro Fixture generated on: 2010-07-13 13:07:19 : 1279053199 */
class AroFixture extends CakeTestFixture {
	var $name = 'Aro';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'model' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'alias' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'aros_lft_rght' => array('column' => array('rght', 'lft'), 'unique' => 0), 'aros_model_fk' => array('column' => array('foreign_key', 'model'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	
	var $records = array(
		array(
			'id' => 1,
			'parent_id' => null,
			'model' => 'Group',
			'foreign_key' => 8,
			'alias' => 'User',
			'lft' => 1,
			'rght' => 2
		)
	);

}
?>