<?php
/* Formatting Test cases generated on: 2010-06-29 13:06:36 : 1277844756 */
App::import('Helper', array('Formatting', 'Html', 'Text', 'Time', 'Number'));

class FormattingHelperTestCase extends CakeTestCase {

	function _prepareAction($action = '') {
		$this->Formatting->params = Router::parse($action);
		$this->Formatting->passedArgs = array_merge($this->Formatting->params['named'], $this->Formatting->params['pass']);
		$this->Formatting->params['url'] = $this->Formatting->params;
		$this->Formatting->beforeFilter();
	}

	function startTest() {
		$this->Formatting = new FormattingHelper();
		$this->Formatting->Html = new HtmlHelper();
		$this->Formatting->Text = new TextHelper();
		$this->Formatting->Time = new TimeHelper();
		$this->Formatting->Number = new NumberHelper();
	}

	function endTest() {
		unset($this->Formatting);
		ClassRegistry::flush();
	}

	function testAddress() {
		$address = array(
			'address_line_1' => '123 Main',
			'address_line_2' => '',
			'city' => 'Somewhere',
			'state' => 'CA',
			'zip' => '',
		);
		$this->assertTags($this->Formatting->address($address), array(
			'span' => array('class' => 'address'),
			'123 Main',
			'br' => array(),
			'Somewhere, CA ',
			'/span'
		));
		
		$address = array(
			'address_line_1' => '123 Main',
			'address_line_2' => 'Ste. 42',
			'city' => 'Somewhere',
			'state' => 'CA',
			'zip' => '12345',
		);
		$expected = <<<TEXT
123 Main
Ste. 42
Somewhere, CA 12345
TEXT;
		$this->assertEqual($this->Formatting->address($address, false), $expected);
	}

	function testReadableDate() {
		$date = array(
			'Date' => array(
				'start_date' => '2010-03-16',
				'end_date' => '2010-03-16',
				'start_time' => '00:00:00',
				'end_time' => '11:59:00',
				'all_day' => 1,
				'permanent' => 1,
				'recurring' => 1,
				'recurrance_type' => 'mw',
				'frequency' => 1,
				'weekday' => 3,
				'day' => 1,
				'offset' => 3
			)
		);
		$result = $this->Formatting->readableDate($date);
		$expected = 'Every month on the 3rd Wednesday starting March 16, 2010 all day';
		$this->assertEqual($result, $expected);

		$date = array(
			'Date' => array(
				'start_date' => '2010-03-16',
				'end_date' => '2010-05-16',
				'start_time' => '16:00:00',
				'end_time' => '18:00:00',
				'all_day' => 0,
				'permanent' => 0,
				'recurring' => 0,
				'recurrance_type' => 'mw',
				'frequency' => 1,
				'weekday' => 3,
				'day' => 1,
				'offset' => 3
			)
		);
		$result = $this->Formatting->readableDate($date);
		$expected = 'March 16, 2010 @ 4:00pm to May 16, 2010 @ 6:00pm';
		$this->assertEqual($result, $expected);

		$date = array(
			'Date' => array(
				'start_date' => '2010-03-16',
				'end_date' => '2010-03-16',
				'start_time' => '16:00:00',
				'end_time' => '18:00:00',
				'all_day' => 0,
				'permanent' => 0,
				'recurring' => 0,
				'recurrance_type' => 'mw',
				'frequency' => 1,
				'weekday' => 3,
				'day' => 1,
				'offset' => 3
			)
		);
		$result = $this->Formatting->readableDate($date);
		$expected = 'March 16, 2010 from 4:00pm to 6:00pm';
		$this->assertEqual($result, $expected);

		$date = array(
			'Date' => array(
				'start_date' => '2010-03-16',
				'end_date' => '2010-03-20',
				'start_time' => '16:00:00',
				'end_time' => '18:00:00',
				'all_day' => 0,
				'permanent' => 0,
				'recurring' => 1,
				'recurrance_type' => 'd',
				'frequency' => 2,
				'weekday' => 3,
				'day' => 1,
				'offset' => 3
			)
		);
		$result = $this->Formatting->readableDate($date);
		$expected = 'Every 2 days from 4:00pm to 6:00pm starting March 16, 2010 until March 20, 2010';
		$this->assertEqual($result, $expected);

		$date = array(
			'Date' => array(
				'start_date' => '2010-03-01',
				'end_date' => '2010-03-31',
				'start_time' => '06:00:00',
				'end_time' => '18:00:00',
				'all_day' => 1,
				'permanent' => 0,
				'recurring' => 1,
				'recurrance_type' => 'md',
				'frequency' => 1,
				'weekday' => 3,
				'day' => 12,
				'offset' => 3
			)
		);
		$result = $this->Formatting->readableDate($date);
		$expected = 'Every month on the 12th starting March 1, 2010 until March 31, 2010 all day';
		$this->assertEqual($result, $expected);
	}

	function testAge() {
		$this->assertEqual('26 yrs.', $this->Formatting->age(26));
		$this->assertEqual('26 yrs.', $this->Formatting->age(26.02));
		$this->assertEqual('26 yrs.', $this->Formatting->age(26.02, true));
		$this->assertEqual('26 yrs., 1 mos.', $this->Formatting->age(26.1, true));
		$this->assertEqual('1 mos.', $this->Formatting->age(.1));
		$this->assertEqual('0 mos.', $this->Formatting->age(.02));
		$this->assertEqual('0 mos.', $this->Formatting->age(.02, true));
	}

	function testFlags() {
		$this->assertNull($this->Formatting->flags());
		$this->assertNull($this->Formatting->flags('NoModel', array('NoModel' => 'nothing')));
		$this->assertError('FormattingHelper::flags - Missing flagging function FormattingHelper::_flagNoModel.');
	}

	function testPhone() {
		$this->assertEqual('(714) 384-0914', $this->Formatting->phone(7143840914));
		$this->assertEqual('(714) 384-0914', $this->Formatting->phone('7143840914'));
		$this->assertEqual('(714) 384-0914', $this->Formatting->phone('714d)_384091-4'));
		$this->assertEqual('384-0914', $this->Formatting->phone('3840914'));
		$this->assertEqual('384-0914', $this->Formatting->phone('(384)09asd14'));
	}

	function testDatetime() {
		$this->assertEqual('2/24/2010 @ 9:55am', $this->Formatting->datetime('2010-02-24 09:55:30'));
		$this->assertNull($this->Formatting->datetime());
	}

	function testDate() {
		$this->assertEqual('2/24/2010', $this->Formatting->date('2010-02-24 09:55:30'));
		$this->assertNull($this->Formatting->date());
	}

	function testTime() {
		$this->assertEqual('9:55am', $this->Formatting->time('2010-02-24 09:55:30'));
		$this->assertNull($this->Formatting->time());
	}

}
?>