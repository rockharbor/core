<?php

$acResults = array();

// format nice for autocomplete
foreach ($results as $result) {
	$acResults[] = array(
		'id' => $result['User']['id'],
		'label' => $result['Profile']['name'].' '.$result['User']['username'],
		'value' => $result['Profile']['name']
	);
}
echo $this->Js->object($acResults);

