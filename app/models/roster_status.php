<?php
class RosterStatus extends AppModel {
	var $name = 'RosterStatus';
	
	var $validate = array(
		'name' => 'notempty'
	);

	var $hasMany = array(
		'Roster'
	);

}
?>