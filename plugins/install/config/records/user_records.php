<?php

App::import('Component', 'Security');

class UserRecords extends Records {

/**
 * The name of the model
 *
 * @var string
 */
	var $name = 'User';

	var $records = null;

	function insert() {
		$this->records = array(
			array(
				'id' => 1,
				'username' => 'admin',
				'password' => Security::hash('password', null, true),
				'active' => 1,
				'flagged' => 0,
				'group_id' => 1,
				'reset_password' => 1,
			)
		);
		parent::insert();
	}
}

?>
