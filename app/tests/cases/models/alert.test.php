<?php
/* Alert Test cases generated on: 2010-06-30 07:06:18 : 1277908338 */
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Alert');

class AlertTestCase extends CoreTestCase {
	function startTest() {
		$this->loadFixtures('Alert', 'Group', 'User', 'AlertsUser');
		$this->Alert =& ClassRegistry::init('Alert');
	}

	function endTest() {
		unset($this->Alert);
		ClassRegistry::flush();
	}

	function testGetReadAlerts() {
		$this->assertEqual($this->Alert->getReadAlerts(1), array(1));
		$this->assertEqual($this->Alert->getReadAlerts(2), array());
	}

	function testGetUnreadAlerts() {
		$this->assertFalse($this->Alert->getUnreadAlerts());
		$this->assertFalse($this->Alert->getUnreadAlerts(1, null));
		$this->assertEqual($this->Alert->getUnreadAlerts(1), array(2,3));
		$this->assertEqual($this->Alert->getUnreadAlerts(1, 1), array(2, 3, 4));
		$this->assertEqual($this->Alert->getUnreadAlerts(1, 1, false), array(2, 3, 4));
		$this->assertEqual($this->Alert->getUnreadAlerts(1, 6), array(2, 3));
		$this->Alert->id = 2;
		$this->Alert->saveField('expires', date('Y-m-d'));
		$this->assertEqual($this->Alert->getUnreadAlerts(1, 1, false), array(2, 3, 4));
		$this->Alert->saveField('expires', date('Y-m-d', strtotime('-1 day')));
		$this->assertEqual($this->Alert->getUnreadAlerts(1, 1, false), array(3, 4));
	}

	function testMarkAsRead() {
		$this->assertFalse($this->Alert->markAsRead());
		$this->assertFalse($this->Alert->markAsRead(1));
		$this->assertFalse($this->Alert->markAsRead(1, 57));
		$this->assertFalse($this->Alert->markAsRead(57, 1));
		$this->assertTrue($this->Alert->markAsRead(1, 3));
		$this->assertTrue($this->Alert->markAsRead(1, 2));
		$this->assertTrue($this->Alert->markAsRead(1, 4));
	}

}
?>