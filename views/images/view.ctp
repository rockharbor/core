<?php

if (!empty($image)) {
	$path = $size.DS.$image['Image']['dirname'].DS.$image['Image']['basename'];
	echo $this->Media->embed($path, array('restrict' => 'image'));
}

?>