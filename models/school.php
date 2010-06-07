<?php
class School extends AppModel {
	
	var $name = 'School';

	var $types = array(
		'e' => 'Elementary School',
		'm' => 'Middle School',
		'h' => 'High School',
		'c' => 'College'
	);

}
?>