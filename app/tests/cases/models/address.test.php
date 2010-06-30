<?php
/* Address Test cases generated on: 2010-06-30 07:06:18 : 1277908218 */
App::import('Model', 'Address');

class AddressTestCase extends CakeTestCase {
	var $fixtures = array('app.address', 'app.zipcode', 'app.region');

	function startTest() {
		$this->Address =& ClassRegistry::init('Address');
	}

	function endTest() {
		unset($this->Address);
		ClassRegistry::flush();
	}

}
?>