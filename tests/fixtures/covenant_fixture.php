<?php

class CovenantFixture extends CakeTestFixture {
	public $name = 'Covenant';
	
	public $fields = array(
		'id' => array('type' => 'integer', 'length' => 8, 'null' => false),
		'user_id' => array('type' => 'integer', 'length' => 8, 'null' => false),
		'year' => array('type' => 'string', 'length' => 9, 'null' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => true), 'user_key' => array('column' => 'user_id'), 'year_key' => array('column' => 'year')),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	
	public $records = array(
		array(
			'id' => 1,
			'user_id' => 1,
			'year' => '2011/2012'
		),
		array(
			'id' => 2,
			'user_id' => 1,
			'year' => '2012/2013'
		),
		array(
			'id' => 3,
			'user_id' => 2,
			'year' => '2011/2012'
		),
		array(
			'id' => 4,
			'user_id' => 2,
			'year' => '2013/2014'
		)
	);
}