<?php
/* Involvement Fixture generated on: 2010-06-28 09:06:25 : 1277741305 */
class InvolvementFixture extends CakeTestFixture {
	var $name = 'Involvement';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'ministry_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'involvement_type_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 64),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'roster_limit' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 3),
		'roster_visible' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'private' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'signup' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'take_payment' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'offer_childcare' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'active' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'force_payment' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'ministry_id' => 4,
			'involvement_type_id' => 1,
			'name' => 'CORE 2.0 testing',
			'description' => 'this is a <b>description</b>',
			'roster_limit' => 5,
			'roster_visible' => 1,
			'private' => NULL,
			'signup' => 1,
			'take_payment' => 1,
			'offer_childcare' => 0,
			'active' => 1,
			'created' => '2010-03-04 13:37:22',
			'modified' => '2010-04-09 13:34:48',
			'force_payment' => 0
		),
		array(
			'id' => 2,
			'ministry_id' => 3,
			'involvement_type_id' => 1,
			'name' => 'Third Wednesday',
			'description' => 'It\'s Third Wednesday, man.',
			'roster_limit' => NULL,
			'roster_visible' => 0,
			'private' => NULL,
			'signup' => 1,
			'take_payment' => 0,
			'offer_childcare' => 1,
			'active' => 0,
			'created' => '2010-03-08 13:27:43',
			'modified' => '2010-04-13 11:27:14',
			'force_payment' => 0
		),
		array(
			'id' => 3,
			'ministry_id' => 4,
			'involvement_type_id' => 3,
			'name' => 'Team CORE',
			'description' => '<i>gooooo </i><b>CORE</b>!',
			'roster_limit' => 5,
			'roster_visible' => 1,
			'private' => NULL,
			'signup' => 1,
			'take_payment' => 1,
			'offer_childcare' => 1,
			'active' => 0,
			'created' => '2010-04-09 14:35:17',
			'modified' => '2010-06-24 19:02:50',
			'force_payment' => 0
		),
		array(
			'id' => 4,
			'ministry_id' => 1,
			'involvement_type_id' => 5,
			'name' => 'Rock Climbing',
			'description' => 'You interested?',
			'roster_limit' => NULL,
			'roster_visible' => 1,
			'private' => NULL,
			'signup' => 1,
			'take_payment' => 1,
			'offer_childcare' => 0,
			'active' => 1,
			'created' => '2010-04-23 13:07:08',
			'modified' => '2010-04-23 13:07:21',
			'force_payment' => 0
		),
		array(
			'id' => 5,
			'ministry_id' => 1,
			'involvement_type_id' => 2,
			'name' => 'Free group',
			'description' => 'Join my group',
			'roster_limit' => NULL,
			'roster_visible' => 1,
			'private' => NULL,
			'signup' => 1,
			'take_payment' => 0,
			'offer_childcare' => 0,
			'active' => 1,
			'created' => '2010-04-23 13:07:08',
			'modified' => '2010-04-23 13:07:21',
			'force_payment' => 0
		)
	);
}
?>