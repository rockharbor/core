<?php
/* GeoCoordinate Test cases generated on: 2010-07-07 07:07:30 : 1278513210 */
App::import('Behavior', 'GeoCoordinate');

class GeoCoordinateBehaviorTestCase extends CakeTestCase {
	var $fixtures = array('app.ministry', 'app.campus', 'plugin.media.attachment',
		'app.leader', 'app.user', 'app.group', 'app.profile', 'app.classification',
		'app.job_category', 'app.school', 'app.comment', 'app.comment_type',
		'app.comments', 'app.notification', 'plugin.media.document', 'app.roster',
		'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode',
		'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.role',
		'app.answer', 'app.payment', 'app.payment_type',
		'app.household_member', 'app.household', 'app.publication',
		'app.publications_user', 'app.log');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
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
?>