<?php

class GroupRecords extends Records {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'Group';

/**
 * Record to import upon install
 *
 * @var array
 */
	var $records = array(
		array(
			'id' => 1,
			'name' => 'Super Administrator',
			'conditional' => 0,
			'parent_id' => NULL,
			'lft' => 1,
			'rght' => 26
		),
		array(
			'id' => 2,
			'name' => 'Administrator',
			'conditional' => 0,
			'parent_id' => 1,
			'lft' => 2,
			'rght' => 25
		),
		array(
			'id' => 3,
			'name' => 'Pastor',
			'conditional' => 0,
			'parent_id' => 2,
			'lft' => 3,
			'rght' => 24
		),
		array(
			'id' => 4,
			'name' => 'Communications Admin',
			'conditional' => 0,
			'parent_id' => 3,
			'lft' => 4,
			'rght' => 23
		),
		array(
			'id' => 5,
			'name' => 'Staff',
			'conditional' => 0,
			'parent_id' => 4,
			'lft' => 5,
			'rght' => 22
		),
		array(
			'id' => 6,
			'name' => 'Intern',
			'conditional' => 0,
			'parent_id' => 5,
			'lft' => 6,
			'rght' => 21
		),
		array(
			'id' => 7,
			'name' => 'Developer',
			'conditional' => 0,
			'parent_id' => 6,
			'lft' => 7,
			'rght' => 20
		),
		array(
			'id' => 8,
			'name' => 'User',
			'conditional' => 0,
			'parent_id' => 7,
			'lft' => 8,
			'rght' => 19
		),
		array(
			'id' => 9,
			'name' => 'Campus Manager',
			'conditional' => 1,
			'parent_id' => 8,
			'lft' => 9,
			'rght' => 14
		),
		array(
			'id' => 10,
			'name' => 'Ministry Manager',
			'conditional' => 1,
			'parent_id' => 9,
			'lft' => 10,
			'rght' => 13
		),
		array(
			'id' => 11,
			'name' => 'Involvement Leader',
			'conditional' => 1,
			'parent_id' => 10,
			'lft' => 11,
			'rght' => 12
		),
		array(
			'id' => 12,
			'name' => 'Owner',
			'conditional' => 1,
			'parent_id' => 8,
			'lft' => 15,
			'rght' => 18
		),
		array(
			'id' => 13,
			'name' => 'Household Contact',
			'conditional' => 1,
			'parent_id' => 12,
			'lft' => 16,
			'rght' => 17
		)
	);

}
