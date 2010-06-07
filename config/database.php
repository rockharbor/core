<?php
class DATABASE_CONFIG {

	var $default = array(
		'driver' => 'mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'core2_db',
		'password' => 'R0ck$^c0r32!',
		'database' => 'core2'
	);
	
	var $test_suite = array(
		'driver' => 'mysql',
		'persistent' => false,
		'host' => 'localhost',
		'login' => 'core2_db',
		'password' => 'R0ck$^c0r32!',
		'database' => 'core2_test'
	);
}
?>