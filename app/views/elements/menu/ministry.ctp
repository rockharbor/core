<?php 
echo $this->Tree->generate($ministries, array(
	'element' => 'menu'.DS.'menu_item',
	'model' => 'Ministry',
	'class' => 'something'
));
?>