<?php
class InterestListType extends AppModel {
	var $name = 'InterestListType';
	
	var $validate = array(
		'name' => 'notempty'
	);

	var $hasMany = array(
		'InterestList'
	);

}
?>