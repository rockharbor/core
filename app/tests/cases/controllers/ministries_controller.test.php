<?php
/* Ministries Test cases generated on: 2010-07-16 08:07:10 : 1279292770 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail', 'Notifier'));
App::import('Controller', 'Ministries');

Mock::generate('QueueEmailComponent');
Mock::generate('NotifierComponent');
Mock::generatePartial('MinistriesController', 'TestMinistriesController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class MinistriesControllerTestCase extends CoreTestCase {
	var $fixtures = array('app.notification', 'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.roster', 'app.role', 'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member', 'app.household', 'app.publication', 'app.publications_user', 'app.log', 'app.app_setting', 'app.alert', 'app.alerts_user', 'app.aro', 'app.aco', 'app.aros_aco', 'app.ministries_rev', 'app.involvements_rev', 'app.error', 'app.log');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		$this->Ministries =& new TestMinistriesController();
		$this->Ministries->constructClasses();
		$this->Ministries->QueueEmail = new MockQueueEmailComponent();
		$this->Ministries->QueueEmail->setReturnValue('send', true);
		$this->Ministries->Notifier = new MockNotifierComponent();
		$this->Ministries->Notifier->setReturnValue('_render', 'Notification body text');
		$this->Ministries->setReturnValue('isAuthorized', true);
		// necessary fixtures
		$this->loadFixtures('Ministry', 'MinistriesRev', 'AppSetting');
		$this->testController = $this->Ministries;
	}

	function endTest() {
		$this->Ministries->Session->destroy();
		unset($this->Ministries);		
		ClassRegistry::flush();
	}

	function testAdd() {
		$data = array(
			'Ministry' => array(
				'name' => 'New Root Ministry',
				'description' => 'Description',
				'parent_id' => 1,
				'campus_id' => 1,
				'group_id' => 8,
				'active' => 1
			)
		);

		$vars = $this->testAction('/ministries/add', array(
			'return' => 'vars',
			'data' => $data
		));
		$ministry = $this->Ministries->Ministry->read(null, 5);
		$result = $ministry['Ministry']['name'];
		$this->assertEqual($result, 'New Root Ministry');

		$results = $vars['ministries'];
		$expected = array(
			1 => 'Communications',
			2 => 'Alpha',
			3 => 'All Church'
		);
		$this->assertEqual($results, $expected);
	}

	function testEdit() {
		$data = $this->Ministries->Ministry->read(null, 1);
		$data['Ministry']['name'] = 'New name';

		$vars = $this->testAction('/ministries/edit/Ministry:1', array(
			'return' => 'vars',
			'data' => $data
		));

		$result = $vars['ministries'];
		$expected = array(
			2 => 'Alpha',
			3 => 'All Church'
		);
		$this->assertEqual($result, $expected);

		$this->Ministries->Ministry->id = 1;
		$result = $this->Ministries->Ministry->field('name');
		$this->assertEqual($result, 'New name');
	}

	function testHistory() {
		$data = $this->Ministries->Ministry->read(null, 1);
		$data['Ministry']['name'] = 'New name';

		$this->Ministries->Ministry->save($data);

		$vars = $this->testAction('/ministries/history/Ministry:1', array(
			'return' => 'vars'
		));

		$result = $vars['revision']['Revision']['id'];
		$this->assertEqual($result, 1);

		$result = $vars['revision']['Revision']['name'];
		$this->assertEqual($result, 'New name');
	}

	function testRevise() {
		$data = $this->Ministries->Ministry->read(null, 1);
		$data['Ministry']['name'] = 'New name';
		$data = array(
			'Revision' => $data['Ministry']
		);

		$this->Ministries->Ministry->RevisionModel->save($data);
		$this->Ministries->Ministry->id = 1;
		$this->testAction('/ministries/revise/0/Ministry:1');
		$result = $this->Ministries->Ministry->RevisionModel->find('all');
		$this->assertFalse($result);
		$result = $this->Ministries->Ministry->field('name');
		$this->assertEqual($result, 'Communications');

		$this->Ministries->Ministry->RevisionModel->save($data);
		$this->Ministries->Ministry->id = 1;
		$this->testAction('/ministries/revise/1/Ministry:1');
		$result = $this->Ministries->Ministry->RevisionModel->find('all');
		$this->assertFalse($result);
		$result = $this->Ministries->Ministry->field('name');
		$this->assertEqual($result, 'New name');
	}

	function testDelete() {
		$this->testAction('/ministries/delete/1');
		$this->assertFalse($this->Ministries->Ministry->read(null, 1));
	}

}
?>