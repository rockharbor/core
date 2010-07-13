<?php
/* Confirm Test cases generated on: 2010-07-06 11:07:24 : 1278439644 */
App::import('Model', 'Ministry');

class ConfirmBehaviorTestCase extends CakeTestCase {
	var $fixtures = array('app.ministry', 'app.campus', 'plugin.media.attachment',
		'app.leader', 'app.user', 'app.group', 'app.profile', 'app.classification',
		'app.job_category', 'app.school', 'app.comment', 'app.comment_type',
		'app.comments', 'app.notification', 'plugin.media.document', 'app.roster',
		'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode',
		'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.role',
		'app.roster_status', 'app.answer', 'app.payment', 'app.payment_type',
		'app.household_member', 'app.household', 'app.publication',
		'app.publications_user', 'app.log', 'app.ministries_rev',
		'app.involvements_rev');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		$this->loadFixtures('Ministry', 'Involvement', 'MinistriesRev', 'InvolvementsRev');
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
				'description' => null,
				'campus_id' => null,
				'group_id' => null,
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