<?php
/* SysEmail Test cases generated on: 2010-08-05 09:08:32 : 1281025892 */
App::import('Lib', 'CoreTestCase');
App::import('Model', array('SysEmail', 'Document'));

class SysEmailTestCase extends CoreTestCase {
	public function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Attachment');
		$this->SysEmail =& ClassRegistry::init('SysEmail');
		$this->Document =& ClassRegistry::init('Document');
		// detach behaviors so they get deleted properly (since the files don't really exist)
		$this->Document->Behaviors->detach('Media.Transfer');
		$this->Document->Behaviors->detach('Media.Polymorphic');
		$this->Document->Behaviors->detach('Media.Coupler');
		ClassRegistry::removeObject('Document');
		ClassRegistry::addObject('Document', $this->Document);
	}

	public function endTest() {
		unset($this->SysEmail);
		unset($this->Document);
		ClassRegistry::flush();
	}

	public function testGcAttachment() {
		$this->SysEmail->gcAttachments('anotherTest');
		$results = $this->Document->find('all', array(
			'fields' => array('id'),
			'conditions' => array(
				'model' => 'SysEmail'
			)
		));
		$this->assertEqual(count($results), 2);

		$attachment = $this->Document->read(null, 1);
		$attachment['Document']['created'] = date('Y-m-d');
		$this->Document->save($attachment);
		$this->SysEmail->gcAttachments();
		$results = $this->Document->find('all', array(
			'fields' => array('id'),
			'conditions' => array(
				'model' => 'SysEmail'
			)
		));
		$this->assertEqual(count($results), 1);
	}

	public function testGcAttachmentsAll() {
		$this->SysEmail->gcAttachments();
		$results = $this->Document->find('all', array(
			'fields' => array('id'),
			'conditions' => array(
				'model' => 'SysEmail'
			)
		));
		$this->assertEqual(count($results), 0);
	}

}
