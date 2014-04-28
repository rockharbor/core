
<?php

// prepare addresses
$addresses = array();
foreach ($results as $result) {
	if (isset($result['ImageIcon'])) {
		$path = 's'.DS.$result['ImageIcon']['dirname'].DS.$result['ImageIcon']['basename'];
		$imageUrl = $this->Media->url($this->Media->file($path));
	} else {
		$imageUrl = null;
	}

	$name = null;
	switch ($model) {
		case 'Roster':
		case 'User':
			$name = $result['Profile']['name'];
		break;
	}

	if (isset($result['ActiveAddress'])) {
		$result['Address'] = $result['ActiveAddress'];
	}

	if (!empty($result['Address'])) {
		$address = $result['Address'];
		if (!isset($address['city'])) {
			$address = $address[0];
		}

		$addresses[] = array(
			'lat' => $address['lat'],
			'lng' => $address['lng'],
			'street' => $address['address_line_1'].' '.$address['address_line_2'],
			'city' => $address['city'],
			'state' => $address['state'],
			'zip' => $address['zip'],
			'name' => !is_null($name) ? $name : $address['name'],
			'image' => $imageUrl
		);
	}
}

if (isset($errorMessage)) {
	echo $errorMessage;
} else {
	echo $this->GoogleMap->create();
	$this->GoogleMap->zoom = 8;
	$this->GoogleMap->addAddresses($addresses);
	echo $this->GoogleMap->end();
}

