<?php
/* Role Fixture generated on: 2010-06-28 09:06:43 : 1277741443 */
class RoleFixture extends CakeTestFixture {
	public $name = 'Role';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'ministry_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'index'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 45),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 200),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'ministry_key' => array('column' => 'ministry_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
		array(
			'id' => 1,
			'ministry_id' => 4,
			'name' => 'Snack Bringer',
			'description' => 'Person who brings the snacks.',
			'created' => '2010-04-13 14:29:18',
			'modified' => '2010-04-13 14:29:18'
		),
		array(
			'id' => 2,
			'ministry_id' => 4,
			'name' => 'Snack Eater',
			'description' => 'Person who eats the snacks!',
			'created' => '2010-04-14 07:16:13',
			'modified' => '2010-04-14 07:16:13'
		),
		array(
			'id' => 3,
			'ministry_id' => 3,
			'name' => 'test',
			'description' => 'test',
			'created' => '2010-04-14 07:16:13',
			'modified' => '2010-04-14 07:16:13'
		),
	);
}
