<?php

if (!empty($image)) {
	$file = $image['Image']['dirname'].DS.$image['Image']['basename'];

	$url = $this->Medium->url($file);
	 
	echo $this->Medium->embed($this->Medium->file($size . '/', $image['Image']), array(
		'restrict' => array('image')
	));
}

?>