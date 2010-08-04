<?php
/* School Test cases generated on: 2010-08-04 13:08:21 : 1280952021 */
App::import('Model', 'School');

class SchoolTestCase extends CakeTestCase {
	var $fixtures = array('app.school');

	var $autoFixtures = false;

	function startTest() {
		$this->School =& ClassRegistry::init('School');
		$this->loadFixtures('School');
	}

	function endTest() {
		unset($this->School);		
		ClassRegistry::flush();
	}

	function testMagicAliases() {
		$this->ElementarySchool =& ClassRegistry::init('School');
		$this->ElementarySchool->alias = 'ElementarySchool';
		$results = $this->ElementarySchool->find('list');
		$expected = array(
			2 => 'East Bluff'
		);
		$this->assertEqual($results, $expected);

		$this->MiddleSchool =& ClassRegistry::init('School');
		$this->MiddleSchool->alias = 'MiddleSchool';
		$results = $this->MiddleSchool->find('list');
		$expected = array(
			3 => 'Adams'
		);
		$this->assertEqual($results, $expected);

		$this->HighSchool =& ClassRegistry::init('School');
		$this->HighSchool->alias = 'HighSchool';
		$results = $this->HighSchool->find('list');
		$expected = array(
			1 => 'El Dorado'
		);
		$this->assertEqual($results, $expected);

		$this->College =& ClassRegistry::init('School');
		$this->College->alias = 'College';
		$results = $this->College->find('list');
		$expected = array(
			4 => 'Azusa Pacific'
		);
		$this->assertEqual($results, $expected);
	}

}
?>