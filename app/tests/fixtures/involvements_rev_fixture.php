<?php
/* InvolvementsRev Fixture generated on: 2010-06-28 09:06:35 : 1277741315 */
class InvolvementsRevFixture extends CakeTestFixture {
	var $name = 'InvolvementsRev';

	var $fields = array(
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
		'indexes' => array('PRIMARY' => array('column' => 'version_id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
	);
}
?>