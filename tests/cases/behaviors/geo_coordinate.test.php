<?php
/* GeoCoordinate Test cases generated on: 2010-07-07 07:07:30 : 1278513210 */
App::import('Lib', 'CoreTestCase');
App::import('Behavior', 'GeoCoordinate');

class GeoCoordinateBehaviorTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Address');
		$this->Address =& ClassRegistry::init('Address');
	}

	function endTest() {
		unset($this->Address);
		ClassRegistry::flush();
	}
	
	function testGeoCoordinates() {
		$address = array(
			'address_line_1' => '3080 Airway',
			'address_line_2' => 'Ste. 100',
			'city' => 'Costa Mesa',
			'state' => 'CA',
			'zip' => '92626'
		);
		$result = $this->Address->Behaviors->GeoCoordinate->geoCoordinates($this->Address, $address);
		$floatReg = '/^[+-]?(([0-9]+)|([0-9]*\.[0-9]+|[0-9]+\.[0-9]*)|(([0-9]+|([0-9]*\.[0-9]+|[0-9]+\.[0-9]*))[eE][+-]?[0-9]+))$/';
		$this->assertPattern($floatReg, $result['lat']);
		$this->assertPattern($floatReg, $result['lng']);
	}

	function testGeoCoordinateBehavior() {
		$floatReg = '/^[+-]?(([0-9]+)|([0-9]*\.[0-9]+|[0-9]+\.[0-9]*)|(([0-9]+|([0-9]*\.[0-9]+|[0-9]+\.[0-9]*))[eE][+-]?[0-9]+))$/';

		$data = array(
			'Address' => array(
				'address_line_1' => '3080 Airway',
				'address_line_2' => 'Ste. 100',
				'city' => 'Costa Mesa',
				'state' => 'CA',
				'zip' => '92626'
			)
		);
		$result = $this->Address->save($data);
		
		$this->assertPattern($floatReg, $result['Address']['lat']);
		$this->assertPattern($floatReg, $result['Address']['lng']);
		
		$oldData = $result;
		$newData = array(
			'Address' => array(
				'address_line_1' => '20048 Santa Ana Avenue',
				'address_line_2' => '',
				'city' => 'Costa Mesa',
				'state' => 'CA',
				'zip' => '92626'
			)
		);
		$data = Set::merge($oldData, $newData);
		$result = $this->Address->save($data);
		$this->assertNotEqual($result['Address']['address_line_1'], '3080 Airway');
		$this->assertNotEqual($oldData, $newData);
		$this->assertPattern($floatReg, $result['Address']['lat']);
		$this->assertPattern($floatReg, $result['Address']['lng']);
	}

}
