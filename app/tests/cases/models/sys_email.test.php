<?php
/* SysEmail Test cases generated on: 2010-08-05 09:08:32 : 1281025892 */
App::import('Lib', 'CoreTestCase');

class SysEmailTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('Attachment', 'Queue');
		$this->SysEmail =& ClassRegistry::init('SysEmail');
		$this->Document =& ClassRegistry::init('Document');
		$this->Queue =& ClassRegistry::init('QueueEmail.Queue');
	}

	function endTest() {
		unset($this->SysEmail);
		unset($this->Document);
		unset($this->Queue);
		ClassRegistry::flush();
	}

	function testGcAttachment() {		
		$attachCount = $this->Document->find('count', array(
			'conditions' => array(
				'model' => 'SysEmail'
			)
		));
		$this->SysEmail->gcAttachments();
		$results = $this->Document->find('count', array(
			'conditions' => array(
				'model' => 'SysEmail'
			)
		));
		$this->assertTrue($attachCount > 0);
		$this->assertEqual($results, $attachCount);

		// simulate the queued emails were sent
		$this->Queue->deleteAll(array('Queue.id > ' => 0));
		$this->assertTrue($this->Queue->find('count') == 0);
		$this->SysEmail->gcAttachments();
		$results = $this->Document->find('count', array(
			'conditions' => array(
				'model' => 'SysEmail'
			)
		));
		$this->assertEqual($results, 0);
	}

}
?>