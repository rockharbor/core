<?php
/* School Test cases generated on: 2010-08-04 13:08:21 : 1280952021 */
App::import('Lib', 'CoreTestCase');
App::import('Model', 'School');

class SchoolTestCase extends CoreTestCase {
	var $fixtures = array('app.school');

	function startTest($method) {
		parent::startTest($method);
		$this->School =& ClassRegistry::init('School');
		$this->loadFixtures('School');
	}

	function endTest() {
		unset($this->School);		
		ClassRegistry::flush();
	}

	function testMagicAliases() {
		$this->ElementarySchool =& ClassRegistry::init(array(
			'class' => 'School',
			'alias' => 'ElementarySchool'
		));
		$results = $this->ElementarySchool->find('list');
		$expected = array(
			2 => 'East Bluff'
		);
		$this->assertEqual($results, $expected);

		$this->MiddleSchool =& ClassRegistry::init(array(
			'class' => 'School',
			'alias' => 'MiddleSchool'
		));
		$results = $this->MiddleSchool->find('list');
		$expected = array(
			3 => 'Adams'
		);
		$this->assertEqual($results, $expected);

		$this->HighSchool =& ClassRegistry::init(array(
			'class' => 'School',
			'alias' => 'HighSchool'
		));
		$results = $this->HighSchool->find('list');
		$expected = array(
			1 => 'El Dorado'
		);
		$this->assertEqual($results, $expected);

		$this->College =& ClassRegistry::init(array(
			'class' => 'School',
			'alias' => 'College'
		));
		$results = $this->College->find('list');
		$expected = array(
			4 => 'Azusa Pacific'
		);
		$this->assertEqual($results, $expected);
	}

}
?>