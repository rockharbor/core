<?php
/* Alert Test cases generated on: 2010-06-30 07:06:18 : 1277908338 */
App::uses('CoreTestCase', 'Lib');
App::uses('Alert', 'Model');

class AlertTestCase extends CoreTestCase {
	public function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Alert', 'Group', 'User', 'AlertsUser');
		$this->Alert =& ClassRegistry::init('Alert');
	}

	public function endTest() {
		unset($this->Alert);
		ClassRegistry::flush();
	}

	public function testGetReadAlerts() {
		$this->assertEqual($this->Alert->getReadAlerts(1), array(1));
		$this->assertEqual($this->Alert->getReadAlerts(2), array());
	}

	public function testGetUnreadAlerts() {
		$this->assertFalse($this->Alert->getUnreadAlerts());
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

	public function testMarkAsRead() {
		$this->assertFalse($this->Alert->markAsRead());
		$this->assertFalse($this->Alert->markAsRead(1));
		$this->assertFalse($this->Alert->markAsRead(1, 57));
		$this->assertFalse($this->Alert->markAsRead(57, 1));
		$this->assertTrue($this->Alert->markAsRead(1, 3));
		$this->assertTrue($this->Alert->markAsRead(1, 2));
		$this->assertTrue($this->Alert->markAsRead(1, 4));
	}

}
