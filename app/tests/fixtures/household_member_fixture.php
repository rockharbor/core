<?php
/* HouseholdMember Fixture generated on: 2010-06-28 09:06:01 : 1277741281 */
class HouseholdMemberFixture extends CakeTestFixture {
	var $name = 'HouseholdMember';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'household_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'confirmed' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 72,
			'household_id' => 9,
			'user_id' => 52,
			'confirmed' => 1,
			'created' => '2010-04-12 10:27:47',
			'modified' => '2010-04-12 10:27:47'
		),
		array(
			'id' => 69,
			'household_id' => 20,
			'user_id' => 52,
			'confirmed' => 1,
			'created' => '2010-04-08 07:35:48',
			'modified' => '2010-04-08 07:35:48'
		),
		array(
			'id' => 67,
			'household_id' => 19,
			'user_id' => 45,
			'confirmed' => 1,
			'created' => '2010-04-07 13:55:09',
			'modified' => '2010-04-07 13:55:09'
		),
		array(
			'id' => 66,
			'household_id' => 19,
			'user_id' => 44,
			'confirmed' => 1,
			'created' => '2010-04-07 13:55:09',
			'modified' => '2010-04-07 13:55:09'
		),
		array(
			'id' => 80,
			'household_id' => 9,
			'user_id' => 1,
			'confirmed' => 1,
			'created' => '2010-05-27 09:15:26',
			'modified' => '2010-05-27 09:15:26'
		),
		array(
			'id' => 74,
			'household_id' => 22,
			'user_id' => 54,
			'confirmed' => 1,
			'created' => '2010-05-06 08:56:36',
			'modified' => '2010-05-06 08:56:36'
		),
		array(
			'id' => 79,
			'household_id' => 21,
			'user_id' => 53,
			'confirmed' => 1,
			'created' => '2010-05-24 10:46:25',
			'modified' => '2010-05-24 10:46:25'
		),
		array(
			'id' => 75,
			'household_id' => 9,
			'user_id' => 54,
			'confirmed' => 1,
			'created' => '2010-05-06 09:06:45',
			'modified' => '2010-05-06 09:06:45'
		),
		array(
			'id' => 81,
			'household_id' => 23,
			'user_id' => 64,
			'confirmed' => 1,
			'created' => '2010-06-02 08:59:40',
			'modified' => '2010-06-02 08:59:40'
		),
		array(
			'id' => 82,
			'household_id' => 24,
			'user_id' => 65,
			'confirmed' => 1,
			'created' => '2010-06-02 10:22:36',
			'modified' => '2010-06-02 10:22:36'
		),
	);
}
?>