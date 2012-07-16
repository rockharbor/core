<?php
/* Confirm Test cases generated on: 2010-07-06 11:07:24 : 1278439644 */
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Ministry');

class ConfirmBehaviorTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Ministry', 'Involvement', 'MinistriesRev');
		$this->Ministry =& ClassRegistry::init('Ministry');
		$this->Ministry->Behaviors->Confirm->settings['Ministry']['fields'] = array();
		$this->Ministry->RevisionModel->useDbConfig = 'test_suite';
		$this->Ministry->Involvement->RevisionModel->useDbConfig = 'test_suite';
	}

	function endTest() {
		unset($this->Ministry);
		ClassRegistry::flush();
	}

	function testSetup() {
		$this->assertIsA($this->Ministry->RevisionModel, 'Model');
		$this->assertIsA($this->Ministry->Behaviors->Confirm->settings['Ministry'], 'array');
		$this->assertIsA($this->Ministry->Behaviors->Confirm->settings['Ministry']['fields'], 'array');
	}
	
	function testFieldsSetting() {
		$this->Ministry->Behaviors->attach('Confirm', array(
			'fields' => array(
				'description'
			)
		));
		
		$this->Ministry->id = 1;
		$this->Ministry->saveField('name', 'Not communications');
		$results = $this->Ministry->field('name');
		$expected = 'Not communications';
		$this->assertEqual($results, $expected);
		
		$this->Ministry->id = 1;
		$this->Ministry->saveField('description', 'A new description');
		$results = $this->Ministry->field('description');
		$expected = 'Description';
		$this->assertEqual($results, $expected);
	}
	
	function testNoChange() {
		$this->Ministry->id = 1;
		$this->Ministry->saveField('name', 'Communications');
		$results = $this->Ministry->field('name');
		$expected = 'Communications';
		$this->assertEqual($results, $expected);

		$rev = $this->Ministry->RevisionModel->find('first', array(
			'conditions' => array(
				'id' => 1
			)
		));
		$this->assertTrue(empty($rev));
		
		$this->assertFalse($this->Ministry->changed());
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
		$this->Ministry->RevisionModel->delete($rev['Revision']['id']);
		
		$this->assertTrue($this->Ministry->changed());
		
		$this->Ministry->id = null;
		$this->Ministry->data = null;
		$this->Ministry->save(array(
			'id' => 1,
			'name' => 'Another change'
		));
		$results = $this->Ministry->field('name');
		$expected = 'Communications';
		$this->assertEqual($results, $expected);
		
		$rev = $this->Ministry->RevisionModel->find('first', array(
			'conditions' => array(
				'id' => 1
			)
		));
		$this->assertEqual($rev['Revision']['name'], 'Another change');
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
		
		$this->assertFalse($this->Ministry->revision());
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
