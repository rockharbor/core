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
		$expected = array(7, 8);
		$this->assertEqual($results, $expected);
		
		$results = $this->Group->findGroups(2, '>');
		$expected = array(1);
		$this->assertEqual($results, $expected);

		$results = $this->Group->findGroups(4, '>=');
		$expected = array(1, 2, 3, 4);
		$this->assertEqual($results, $expected);

		$results = $this->Group->findGroups(5, '<=');
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