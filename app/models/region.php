<?php
class Region extends AppModel {
	var $name = 'Region';
	
	var $validate = array(
		'name' => 'notempty'
	);

	var $hasMany = array(
		'Zipcode' => array(
			'dependent' => true
		)
	);

}
?>