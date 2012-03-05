<?php
/* Group Test cases generated on: 2010-07-13 09:07:53 : 1279039973 */
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Group');

class GroupTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Group');
		$this->Group =& ClassRegistry::init('Group');
		$this->loadSettings();
	}

	function endTest() {
		$this->unloadSettings();
		unset($this->Group);		
		ClassRegistry::flush();
	}

	function testFindGroups() {
		$results = $this->Group->findGroups(7);
		$expected = array(
			7 => 'Developer',
			8 => 'User'
		);
		$this->assertEqual($results, $expected);
		
		$results = Set::extract('/Group/id', $this->Group->findGroups(2, 'all', '>'));
		$expected = array(1);
		$this->assertEqual($results, $expected);

		$results = $this->Group->findGroups(4, 'list', '>=');
		$expected = array(
			1 => 'Super Administrator',
			2 => 'Administrator',
			3 => 'Pastor',
			4 => 'Communications Admin'
		);
		$this->assertEqual($results, $expected);

		$results = Set::extract('/Group/id', $this->Group->findGroups(5, 'all', '<='));
		$expected = array(5, 6, 7, 8);
		$this->assertEqual($results, $expected);
	}

	function testCanSeePrivate() {
		$result = $this->Group->canSeePrivate(8);
		$this->assertFalse($result);

		$result = $this->Group->canSeePrivate(9);
		$this->assertFalse($result);

		$result = $this->Group->canSeePrivate(2);
		$this->assertTrue($result);

		$result = $this->Group->canSeePrivate(3);
		$this->assertTrue($result);
	}

}
?>