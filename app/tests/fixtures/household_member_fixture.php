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
			'id' => 1,
			'household_id' => 1,
			'user_id' => 1,
			'confirmed' => 1,
			'created' => '2010-04-12 10:27:47',
			'modified' => '2010-04-12 10:27:47'
		),
		array(
			'id' => 2,
			'household_id' => 2,
			'user_id' => 2,
			'confirmed' => 1,
			'created' => '2010-04-08 07:35:48',
			'modified' => '2010-04-08 07:35:48'
		),
		array(
			'id' => 3,
			'household_id' => 2,
			'user_id' => 3,
			'confirmed' => 1,
			'created' => '2010-04-07 13:55:09',
			'modified' => '2010-04-07 13:55:09'
		),
		array(
			'id' => 4,
			'household_id' => 1,
			'user_id' => 3,
			'confirmed' => 1,
			'created' => '2010-04-07 13:55:09',
			'modified' => '2010-04-07 13:55:09'
		),
		array(
			'id' => 5,
			'household_id' => 3,
			'user_id' => 3,
			'confirmed' => 1,
			'created' => '2010-04-07 13:55:09',
			'modified' => '2010-04-07 13:55:09'
		),
		array(
			'id' => 6,
			'household_id' => 6,
			'user_id' => 6,
			'confirmed' => 1,
			'created' => '2010-04-07 13:55:09',
			'modified' => '2010-04-07 13:55:09'
		)
	);
}
?>