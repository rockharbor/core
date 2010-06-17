
<?php

// prepare addresses
$addresses = array();
foreach ($results as $result) {
	if (!empty($result['Image'])) {
		$imageUrl = $this->Medium->url($this->Medium->file('s/', $result['Image'][0]));
	} else {
		$imageUrl = null;
	}
	
	if (!empty($result['Address'])) {
	$addresses[] = array(
		'lat' => $result['Address'][0]['lat'],
		'lng' => $result['Address'][0]['lng'],
		'street' => $result['Address'][0]['address_line_1'].' '.$result['Address'][0]['address_line_2'],
		'city' => $result['Address'][0]['city'],
		'state' => $result['Address'][0]['state'],
		'zip' => $result['Address'][0]['zip'],
		'name' => $result['Profile']['name'],
		'image' => $imageUrl
	);
	}
}

echo $this->GoogleMap->create();
$this->GoogleMap->zoom = 8;
$this->GoogleMap->addAddresses($addresses);
echo $this->GoogleMap->end();

?>