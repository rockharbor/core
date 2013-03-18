<?php

$default = array(
	'image' => null,
	'name' => null,
	'city' => null,
	'state' => null,
	'zip' => null
);

$data = array_merge($default, $data);

foreach ($data as &$datum) {
	$datum = str_replace(array("\r", "\r\n", "\n"), '', $datum);
}

extract($data);

if (!empty($image)) {
	echo $this->Html->image($image, array(
		'align' => 'left',
		'style' => 'margin-right:7px'
	));
}

echo $this->Html->tag('strong', $name);
echo '<br>';
echo $street;
echo '<br>';
echo $city.', '.$state.' '.$zip;
