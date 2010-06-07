<?php
class InvolvementRequestType extends AppModel {
	var $name = 'InvolvementRequestType';
	
	var $validate = array(
		'name' => 'notempty'
	);
	
	var $belongsTo = array('User');
}
?>