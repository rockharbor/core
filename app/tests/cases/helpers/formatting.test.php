<?php
/* Formatting Test cases generated on: 2010-06-29 13:06:36 : 1277844756 */
App::import('Helper', 'Formatting');

class FormattingHelperTestCase extends CakeTestCase {
	
	function _prepareAction($action = '') {
		$this->Formatting->params = Router::parse($action);
		$this->Formatting->passedArgs = array_merge($this->Formatting->params['named'], $this->Formatting->params['pass']);
		$this->Formatting->params['url'] = $this->Formatting->params;
		$this->Formatting->beforeFilter();
	}

	function startTest() {
		$this->Formatting =& new FormattingHelper();
	}

	function endTest() {
		unset($this->Formatting);
		ClassRegistry::flush();
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

	function testDate() {
		$this->assertEqual('2/24/2010 @ 9:55am', $this->Formatting->date('2010-02-24 09:55:30'));
		$this->assertNull($this->Formatting->date());
	}

}
?>