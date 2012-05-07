<?php

class ProfileRecords extends Records {
	
	/**
	 *The name of the model
	 * 
	 * @var string 
	 */
	protected $name = 'Profile';
	
	/**
	 *Records to insert upon install
	 * 
	 * @var array 
	 */
	protected $records = array(
		array(
			'id' => 1,
			'user_id' => 1,
			'adult' => 1,
			'primary_email' => 'admin@example.com',
			'qualified_leader' => 1,
			'created_by' => 0,
			'created_by_type' => 0,
			'campus_id' => 1,
			'email_on_notification' => 0
		)
	);
}