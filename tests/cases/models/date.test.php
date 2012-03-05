<?php
/* Date Test cases generated on: 2010-06-30 10:06:30 : 1277920170 */
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Date');

class DateTestCase extends CoreTestCase {
	function startTest($method) {
		parent::startTest($method);
		$this->Date =& ClassRegistry::init('Date');
	}

	function endTest() {
		unset($this->Date);
		ClassRegistry::flush();
	}

	function testVirtualFields() {
		$this->loadFixtures('Date');
		
		$date = $this->Date->read(null, 1);
		$result = $date['Date']['previous'];
		$this->assertFalse($result);

		$date = $this->Date->read(null, 2);
		$result = $date['Date']['previous'];
		$this->assertTrue($result);

		$date = $this->Date->read(null, 2);
		$date['Date']['end_date'] = date('Y-m-d', strtotime('+1 day'));
		$this->Date->save($date);
		$date = $this->Date->read(null, 2);
		$result = $date['Date']['previous'];
		$this->assertFalse($result);
	}
	
	function testSingleOnly() {
		$this->loadFixtures('Date');
		
		$dates = $this->Date->generateDates(3, array(
			'start' => mktime(0, 0, 0, 6, 1, 2000),
			'end' => mktime(0, 0, 0, 6, 1, 2020),
			'single' => true
		));
		$this->assertEqual($dates, array());
		
		$dates = $this->Date->generateDates(8, array(
			'start' => mktime(0, 0, 0, 6, 1, 2000),
			'end' => mktime(0, 0, 0, 6, 1, 2020),
			'single' => true
		));
		$results = array_unique(Set::extract('/Date/id', $dates));
		$expected = array(12);
		$this->assertEqual($results, $expected);
	}

	function testGenerateDates() {
		$this->loadFixtures('Date');

		$this->assertFalse($this->Date->generateDates());

		$results = $this->Date->generateDates(2, array(
			'start' => mktime(0, 0, 0, 6, 1, 2010),
			'end' => mktime(0, 0, 0, 6, 31, 2010)
		));
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-06-08',
			'2010-06-16'
		);
		$this->assertEqual($results, $expected);

		$results = $this->Date->generateDates(2, array(
			'start' => '6/1/2010',
			'end' => '6/31/2010'
		));
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-06-08',
			'2010-06-16'
		);
		$this->assertEqual($results, $expected);
	}

	function testLimit() {
		$this->loadFixtures('Date');

		$results = $this->Date->generateDates(5, array(
			'start' => mktime(0, 0, 0, 7, 1, 2010),
			'end' => mktime(0, 0, 0, 7, 31, 2010),
			'limit' => 5
		));
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-07-01',
			'2010-07-02',
			'2010-07-03',
			'2010-07-04',
			'2010-07-05'
		);
		$this->assertEqual($results, $expected);

		$results = $this->Date->generateDates(5, array(
			'start' => mktime(0, 0, 0, 7, 5, 2010),
			'limit' => 5
		));
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-07-05',
			'2010-07-06',
			'2010-07-07',
			'2010-07-08',
			'2010-07-09'
		);
		$this->assertEqual($results, $expected);

		$results = $this->Date->generateDates(5, array(
			'start' => mktime(0, 0, 0, 7, 15, 2010),
			'limit' => 5
		));
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-07-15',
			'2010-07-16',
			'2010-07-17',
			'2010-07-19',
			'2010-07-31'
		);
		$this->assertEqual($results, $expected);
	}

	function testLimitWithRecurringExemption() {
		$this->loadFixtures('Date');

		$results = $this->Date->generateDates(5, array(
			'start' => mktime(0, 0, 0, 7, 5, 2010),
			'limit' => 5
		));
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-07-05',
			'2010-07-06',
			'2010-07-07',
			'2010-07-08',
			'2010-07-09'
		);
		$this->assertEqual($results, $expected);

		$results = $this->Date->generateDates(5, array(
			'start' => mktime(0, 0, 0, 7, 18, 2010),
			'limit' => 5
		));
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-07-19',
			'2010-07-31'
		);
		$this->assertEqual($results, $expected);
		
		// make it a permanent recurring exemption
		$this->Date->save(array(
			'Date' => array(
				'id' => 11,
				'recurring' => true,
				'permanent' => true
			)
		));
		
		$results = $this->Date->generateDates(5, array(
			'start' => mktime(0, 0, 0, 7, 17, 2010),
			'limit' => 1
		));
		$this->assertEqual(count($results), 1);
	}
	
	function testMonthlyWithExemption() {
		$this->loadFixtures('Date');
		
		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 1, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 6, 2, 2010))
		);
		$results = $this->Date->generateDates(4, $range);
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-04-06',
			'2010-06-01'
		);
		$this->assertEqual($results, $expected);
	}

	function testYearlyRecurringDates() {
		$date = array(
			'Date' => array(
				'start_date' => '2010-04-05',
				'end_date' => '2012-06-20',
				'start_time' => '08:00:00',
				'end_time' => '11:00:00',
				'all_day' => 0,
				'permanent' => 0,
				'recurring' => 1,
				'recurrance_type' => 'y',
				'frequency' => 1,
				'weekday' => 3,
				'day' => 12,
				'exemption' => 0,
				'offset' => 1
			)
		);

		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 1, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 1, 1, 2011))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-04-05'
		);
		$this->assertEqual($results, $expected);

		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 1, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 5, 1, 2012))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-04-05',
			'2011-04-05',
			'2012-04-05'
		);
		$this->assertEqual($results, $expected);

		$date['Date']['frequency'] = 2;
		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 6, 1, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 7, 1, 2012))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2012-04-05'
		);
		$this->assertEqual($results, $expected);
	}

	function testWeeklyRecurringDates() {
		$date = array(
			'Date' => array(
				'start_date' => '2010-04-05',
				'end_date' => '2010-06-20',
				'start_time' => '08:00:00',
				'end_time' => '11:00:00',
				'all_day' => 0,
				'permanent' => 0,
				'recurring' => 1,
				'recurrance_type' => 'w',
				'frequency' => 1,
				'weekday' => 3,
				'day' => 12,
				'exemption' => 0,
				'offset' => 1
			)
		);

		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 1, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 11, 2010))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-04-07'
		);
		$this->assertEqual($results, $expected);

		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 1, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 20, 2010))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-04-07',
			'2010-04-14'
		);
		$this->assertEqual($results, $expected);

		$date['Date']['frequency'] = 3;
		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 6, 1, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 6, 10, 2010))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-06-09'
		);
		$this->assertEqual($results, $expected);
	}

	function testMonthWeekdayRecurringDates() {
		$date = array(
			'Date' => array(
				'start_date' => '2010-04-05',
				'end_date' => '2010-05-20',
				'start_time' => '08:00:00',
				'end_time' => '11:00:00',
				'all_day' => 0,
				'permanent' => 0,
				'recurring' => 1,
				'recurrance_type' => 'mw',
				'frequency' => 1,
				'weekday' => 2,
				'day' => 12,
				'exemption' => 0,
				'offset' => 1
			)
		);

		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 1, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 11, 2010))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-04-06'
		);
		$this->assertEqual($results, $expected);

		$date['Date']['offset'] = 2;
		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 1, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 11, 2010))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$this->assertEqual($results, array());

		$date['Date']['frequency'] = 2;
		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 1, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 6, 17, 2010))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-04-13'
		);
		$this->assertEqual($results, $expected);

		$date['Date']['end_date'] = '2010-07-01';
		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 1, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 6, 17, 2010))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-04-13',
			'2010-06-08'
		);
		$this->assertEqual($results, $expected);
	}

	function testMonthDateRecurringDates() {
		$date = array(
			'Date' => array(
				'start_date' => '2010-04-05',
				'end_date' => '2010-05-20',
				'start_time' => '08:00:00',
				'end_time' => '11:00:00',
				'all_day' => 0,
				'permanent' => 0,
				'recurring' => 1,
				'recurrance_type' => 'md',
				'frequency' => 1,
				'weekday' => 2,
				'day' => 12,
				'exemption' => 0,
				'offset' => 2
			)
		);

		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 1, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 5, 1, 2010))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-04-12'
		);
		$this->assertEqual($results, $expected);

		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 1, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 6, 1, 2010))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_date', $results);
		$expected[] = '2010-05-12';
		$this->assertEqual($results, $expected);

		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 5, 1, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 7, 1, 2010))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-05-12'
		);
		$this->assertEqual($results, $expected);
	}

	function testNonRecurringDates() {
		$date = array(
			'Date' => array(
				'start_date' => '2010-04-05',
				'end_date' => '2010-05-05',
				'start_time' => '08:00:00',
				'end_time' => '11:00:00',
				'all_day' => 0,
				'permanent' => 0,
				'recurring' => 0,
				'recurrance_type' => 'mw',
				'frequency' => 2,
				'weekday' => 2,
				'day' => 1,
				'exemption' => 0,
				'offset' => 2
			)
		);
		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 1, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 5, 1, 2010))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$this->assertEqual($results, array($date));

		$range['start'] = '2010-04-10';
		$results = $this->Date->_generateRecurringDates($date, $range);
		$this->assertEqual($results, array($date));

		$range['start'] = '2010-06-01';
		$range['end'] = '2010-08-01';
		$results = $this->Date->_generateRecurringDates($date, $range);
		$this->assertEqual($results, array());
	}

	function testDailyRecurringDate() {
		$date = array(
			'Date' => array(
				'start_date' => '2010-04-05',
				'end_date' => '2010-04-10',
				'start_time' => '08:00:00',
				'end_time' => '11:00:00',
				'all_day' => 0,
				'permanent' => 0,
				'recurring' => 1,
				'recurrance_type' => 'd',
				'frequency' => 1,
				'weekday' => 2,
				'day' => 1,
				'exemption' => 0,
				'offset' => 2
			)
		);

		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 5, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 7, 2010))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-04-05',
			'2010-04-06'
		);
		$this->assertEqual($results, $expected);

		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 5, 2010)),
			'end' => date('Y-m-d H:i', mktime(12, 0, 0, 4, 7, 2010))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_date', $results);
		$expected[] ='2010-04-07';
		$this->assertEqual($results, $expected);
	}

	function testHourlyRecurringDate() {
		$date = array(
			'Date' => array(
				'start_date' => '2010-04-05',
				'end_date' => '2010-05-05',
				'start_time' => '08:00:00',
				'end_time' => '11:00:00',
				'all_day' => 0,
				'permanent' => 0,
				'recurring' => 1,
				'recurrance_type' => 'h',
				'frequency' => 4,
				'weekday' => 2,
				'day' => 1,
				'exemption' => 0,
				'offset' => 2
			)
		);

		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 4, 2010)),
			'end' => date('Y-m-d H:i', mktime(13, 0, 0, 4, 5, 2010))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_time', $results);
		$expected = array(
			'08:00:00',
			'12:00:00'
		);
		$this->assertEqual($results, $expected);

		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 5, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 6, 2010))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_time', $results);
		$expected = array(
			'08:00:00',
			'12:00:00',
			'16:00:00',
			'20:00:00'
		);
		$this->assertEqual($results, $expected);
	}

	function testOutOfRangeDate() {
		$date = array(
			'Date' => array(
				'start_date' => '2010-04-05',
				'end_date' => '2010-04-10',
				'start_time' => '08:00:00',
				'end_time' => '11:00:00',
				'all_day' => 0,
				'permanent' => 0,
				'recurring' => 1,
				'recurrance_type' => 'd',
				'frequency' => 1,
				'weekday' => 2,
				'day' => 1,
				'exemption' => 0,
				'offset' => 2
			)
		);

		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 1, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 4, 2010))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$this->assertEqual($results, array());

		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 11, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 11, 2011))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$this->assertEqual($results, array());

		$date['Date']['recurrance_type'] = 'y';
		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 11, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 4, 1, 2011))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$this->assertEqual($results, array());
	}
	
	function testRangeAsTime() {
		$date = array(
			'Date' => array(
				'start_date' => '2010-04-05',
				'end_date' => '2010-05-20',
				'start_time' => '08:00:00',
				'end_time' => '11:00:00',
				'all_day' => 0,
				'permanent' => 0,
				'recurring' => 1,
				'recurrance_type' => 'md',
				'frequency' => 1,
				'weekday' => 2,
				'day' => 12,
				'exemption' => 0,
				'offset' => 2
			)
		);
		
		$range = array(
			'start' => mktime(0, 0, 0, 5, 1, 2010),
			'end' => mktime(0, 0, 0, 7, 1, 2010)
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-05-12'
		);
		$this->assertEqual($results, $expected);
		
		$range = array(
			'start' => (string)mktime(0, 0, 0, 5, 1, 2010),
			'end' => (string)mktime(0, 0, 0, 7, 1, 2010)
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-05-12'
		);
		$this->assertEqual($results, $expected);
	}
	
	function testRangeAsString() {
		$date = array(
			'Date' => array(
				'start_date' => '2010-04-05',
				'end_date' => '2010-05-20',
				'start_time' => '08:00:00',
				'end_time' => '11:00:00',
				'all_day' => 0,
				'permanent' => 0,
				'recurring' => 1,
				'recurrance_type' => 'md',
				'frequency' => 1,
				'weekday' => 2,
				'day' => 12,
				'exemption' => 0,
				'offset' => 2
			)
		);
		
		$range = array(
			'start' => date('Y-m-d H:i', mktime(0, 0, 0, 5, 1, 2010)),
			'end' => date('Y-m-d H:i', mktime(0, 0, 0, 7, 1, 2010))
		);
		$results = $this->Date->_generateRecurringDates($date, $range);
		$results = Set::extract('/Date/start_date', $results);
		$expected = array(
			'2010-05-12'
		);
		$this->assertEqual($results, $expected);
	}

}
?>