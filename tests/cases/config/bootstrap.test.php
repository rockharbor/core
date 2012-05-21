<?php

App::import('Lib', 'CoreTestCase');

class BootstrapTestCase extends CoreTestCase {
	
	function testBr2nl() {
		$text = 'This<br />is some<br>text.';
		$result = br2nl($text);
		$expected = <<<TEXT
This
is some
text.
TEXT;
		$this->assertEqual($result, $expected);
	}
	
	function testArrayFilterRecursive() {
		$array = array(
			'User' => array(
				'Profile' => array(
					'user_id' => null, 
					'primary_email' => 'some value'
				),
				'Roster' => array(
					'user_id' => 1, 
					'id' => 0, 
					'roster_status_id' => 0,
					'RosterStatus' => array(
						'name' => 1
					)
				)
			)
		);
		$result = array_filter_recursive($array);
		$expected = array(
			'User' => array(
				'Profile' => array(
					'primary_email' => 'some value'
				),
				'Roster' => array(
					'user_id' => 1, 
					'RosterStatus' => array(
						'name' => 1
					)
				)
			)
		);
		$this->assertEqual($result, $expected);
		
		$this->assertEqual($array['User']['Profile']['user_id'], null);
	}
	
}
