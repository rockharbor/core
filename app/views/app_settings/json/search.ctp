<?php

$acResults = array();

// format nice for autocomplete
foreach ($results as $id => $label) {
	$acResults[] = array(
		'id' => $id,
		'label' => $label
	);
}
echo json_encode($acResults);

?>