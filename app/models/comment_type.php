<?php
class CommentType extends AppModel {
	var $name = 'CommentType';
	
	var $validate = array(
		'name' => 'notempty'
	);

	var $hasMany = array(
		'Comments'
	);
	
	var $belongsTo = array(
		'Group'
	);

}
?>