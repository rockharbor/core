<?php
/* Confirm Test cases generated on: 2010-07-06 11:07:24 : 1278439644 */
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Ministry');

class ConfirmBehaviorTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('Ministry', 'Involvement', 'MinistriesRev');
		$this->Ministry =& ClassRegistry::init('Ministry');
		$this->Ministry->RevisionModel->useDbConfig = 'test_suite';
		$this->Ministry->Involvement->RevisionModel->useDbConfig = 'test_suite';
	}

	function endTest() {
		unset($this->Ministry);
		ClassRegistry::flush();
	}

	function testSetup() {
		$this->assertIsA($this->Ministry->RevisionModel, 'Model');
	}

	function testConfirm() {
		$this->Ministry->id = 1;
		$this->Ministry->saveField('name', 'Revised Name');
		$results = $this->Ministry->field('name');
		$expected = 'Communications';
		$this->assertEqual($results, $expected);

		$rev = $this->Ministry->RevisionModel->find('first', array(
			'conditions' => array(
				'id' => 1
			)
		));
		$this->assertEqual($rev['Revision']['name'], 'Revised Name');
	}
	
	function testConfirmDisabled() {
		$this->Ministry->Behaviors->disable('Confirm');
		$this->Ministry->id = 1;
		$this->Ministry->saveField('description', 'Revised Description');
		$results = $this->Ministry->field('description');
		$expected = 'Revised Description';
		$this->assertEqual($results, $expected);

		$rev = $this->Ministry->RevisionModel->find('first', array(
			'conditions' => array(
				'id' => 1
			)
		));
		$this->assertFalse($rev);
	}

	function testRevision() {
		$this->Ministry->id = 1;
		$this->Ministry->read();
		$success = $this->Ministry->save(array(
			'name' => 'Revised Name',
			'active' => false
		));
		$this->assertTrue($success);
		$results = $this->Ministry->revision(1);		
		$expected = array(
			'Revision' => array(
				'version_id' => 1,
				'id' => 1,
				'name' => 'Revised Name',
				'description' => 'Description',
				'campus_id' => 1,
				'private' => 0,
				'active' => 0,
				'parent_id' => null
			)
		);
		unset($results['Revision']['version_created']);
		$this->assertEqual($results, $expected);
	}

	function testConfirmRevision() {
		$this->Ministry->id = 1;
		$this->Ministry->save(array(
			'name' => 'Revised Name',
			'active' => false
		));
		$this->assertTrue($this->Ministry->confirmRevision(1));

		$data = $this->Ministry->read();
		$this->assertEqual($data['Ministry']['name'], 'Revised Name');
		$this->assertFalse($this->Ministry->RevisionModel->find('all'));
	}

	function testDenyRevision() {
		$this->Ministry->id = 1;
		$this->Ministry->save(array(
			'name' => 'Revised Name',
			'active' => false
		));
		$this->assertTrue($this->Ministry->denyRevision(1));

		$data = $this->Ministry->read();
		$this->assertEqual($data['Ministry']['name'], 'Communications');
		$this->assertFalse($this->Ministry->RevisionModel->find('all'));
	}



}
?>