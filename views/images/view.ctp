<?php

if (!empty($image)) {
	$file = $image['Image']['dirname'].DS.$image['Image']['basename'];

	$url = $this->Media->url($file);
	 
	echo $this->Media->embed($this->Media->file($size . '/', $image['Image']), array(
		'restrict' => array('image')
	));
}

?>