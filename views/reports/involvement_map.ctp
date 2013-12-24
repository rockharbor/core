
<?php

// prepare addresses
$addresses = array();
foreach ($results as $result) {
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
			'name' => $address['name']
		);
	}
}

echo $this->GoogleMap->create();
$this->GoogleMap->zoom = 10;
$this->GoogleMap->addAddresses($addresses);
echo $this->GoogleMap->end();

