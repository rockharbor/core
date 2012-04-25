<?php
App::import('Helper', array('SelectOptions'));

class SelectOptionsHelperTestCase extends CakeTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->SelectOptions = new SelectOptionsHelper();
	}

	function endTest() {
		unset($this->SelectOptions);
		ClassRegistry::flush();
	}
	
	function testGenerateOptions() {
		$results = $this->SelectOptions->generateOptions('minute', array('interval' => 15));
		$expected = array(
			'00' => '00',
			'15' => '15',
			'30' => '30',
			'45' => '45',
		);
		$this->assertEqual($results, $expected);
		
		$results = $this->SelectOptions->generateOptions('hour');
		$expected = array(
			'01' => '01',
			'02' => '02',
			'03' => '03',
			'04' => '04',
			'05' => '05',
			'06' => '06',
			'07' => '07',
			'08' => '08',
			'09' => '09',
			'10' => '10',
			'11' => '11',
			'12' => '12',
		);
		$this->assertEqual($results, $expected);
		
		$results = $this->SelectOptions->generateOptions('hour24');
		$expected = array(
			'00' => '00',
			'01' => '01',
			'02' => '02',
			'03' => '03',
			'04' => '04',
			'05' => '05',
			'06' => '06',
			'07' => '07',
			'08' => '08',
			'09' => '09',
			'10' => '10',
			'11' => '11',
			'12' => '12',
			'13' => '13',
			'14' => '14',
			'15' => '15',
			'16' => '16',
			'17' => '17',
			'18' => '18',
			'19' => '19',
			'20' => '20',
			'21' => '21',
			'22' => '22',
			'23' => '23',
		);
		$this->assertEqual($results, $expected);
		
		$results = $this->SelectOptions->generateOptions('meridian');
		$expected = array(
			'am' => 'am',
			'pm' => 'pm',
		);
		$this->assertEqual($results, $expected);
		
		$results = $this->SelectOptions->generateOptions('day', array('min' => 5, 'max' => 10));
		$expected = array(
			'05' => '05',
			'06' => '06',
			'07' => '07',
			'08' => '08',
			'09' => '09',
			'10' => '10',
		);
		$this->assertEqual($results, $expected);
		
		$results = $this->SelectOptions->generateOptions('week');
		$expected = array(
			'0' => 'Sunday',
			'1' => 'Monday',
			'2' => 'Tuesday',
			'3' => 'Wednesday',
			'4' => 'Thursday',
			'5' => 'Friday',
			'6' => 'Saturday',
		);
		$this->assertEqual($results, $expected);
		
		$results = $this->SelectOptions->generateOptions('month');
		$expected = array(
			'01' => 'January',
			'02' => 'February',
			'03' => 'March',
			'04' => 'April',
			'05' => 'May',
			'06' => 'June',
			'07' => 'July',
			'08' => 'August',
			'09' => 'September',
			'10' => 'October',
			'11' => 'November',
			'12' => 'December',
		);
		$this->assertEqual($results, $expected);
		
		$results = $this->SelectOptions->generateOptions('year', array('min' => 2000, 'max' => 2010));
		$expected = array(
			'2000' => '2000',
			'2001' => '2001',
			'2002' => '2002',
			'2003' => '2003',
			'2004' => '2004',
			'2005' => '2005',
			'2006' => '2006',
			'2007' => '2007',
			'2008' => '2008',
			'2009' => '2009',
			'2010' => '2010',
		);
		$this->assertEqual($results, $expected);
	}

	function testValue() {
		$View = new View(new Controller());
		$View->set('jobCategories', array(1 => 'Web Development'));

		$profile = array(
			'Profile' => array(
				'job_category_id' => 1
			)
		);
		$result = $this->SelectOptions->value('Profile.job_category_id', $profile);
		$this->assertEqual($result, 'Web Development');

		$profile['Profile']['job_category_id'] = 2;
		$result = $this->SelectOptions->value('Profile.job_category_id', $profile);
		$this->assertEqual($result, '&nbsp;');

		$result = $this->SelectOptions->value('Profile.job_category_id', $profile, null);
		$this->assertNull($result);

		unset($View->viewVars['jobCategories']);
		$result = $this->SelectOptions->value('Profile.job_category_id', $profile);
		$this->assertEqual($result, 2);

		$results = $this->SelectOptions->value('Profile.anything_without_data');
		$this->assertEqual($results, '&nbsp;');
	}

	function testMagicCall() {
		$this->assertFalse(method_exists($this->SelectOptions, 'gender'));
		$this->assertEqual($this->SelectOptions->gender('m'), 'Male');

		$this->assertFalse(method_exists($this->SelectOptions, 'maritalStatus'));
		$this->assertEqual($this->SelectOptions->maritalStatus('d'), 'Divorced');

		$this->assertFalse(method_exists($this->SelectOptions, 'grade'));
		$this->assertEqual($this->SelectOptions->grade(-1), 'Pre-kinder');

		$this->expectError();
		$this->assertFalse($this->SelectOptions->nonExistentMap);
	}

}
?>