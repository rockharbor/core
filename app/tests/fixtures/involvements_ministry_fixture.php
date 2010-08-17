<?php
/* InvolvementsMinistry Fixture generated on: 2010-08-16 08:08:48 : 1281974328 */
class InvolvementsMinistryFixture extends CakeTestFixture {
	var $name = 'InvolvementsMinistry';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'involvement_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'ministry_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'involvement_id' => 4,
			'ministry_id' => 4
		),
		array(
			'id' => 2,
			'involvement_id' => 4,
			'ministry_id' => 3
		),
		array(
			'id' => 3,
			'involvement_id' => 3,
			'ministry_id' => 3
		)
	);
}
?>