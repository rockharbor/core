<?php
class JobCategory extends AppModel {
	var $name = 'JobCategory';
	
	var $validate = array(
		'name' => 'notempty'
	);

}
?>