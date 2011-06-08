<?php
/* Ministries Test cases generated on: 2010-07-16 08:07:10 : 1279292770 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail.QueueEmail', 'Notifier'));
App::import('Controller', 'Ministries');

Mock::generatePartial('QueueEmailComponent', 'MockQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('NotifierComponent', 'MockNotifierComponent', array('_render'));
Mock::generatePartial('MinistriesController', 'TestMinistriesController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class MinistriesControllerTestCase extends CoreTestCase {

	function startTest() {
		// necessary fixtures
		$this->loadSettings();
		$this->loadFixtures('Ministry', 'MinistriesRev');
		$this->Ministries =& new TestMinistriesController();
		$this->Ministries->__construct();
		$this->Ministries->constructClasses();
		$this->Ministries->Notifier = new MockNotifierComponent();
		$this->Ministries->Notifier->initialize($this->Ministries);
		$this->Ministries->Notifier->setReturnValue('_render', 'Notification body text');
		$this->Ministries->Notifier->QueueEmail = new MockQueueEmailComponent();
		$this->Ministries->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Ministries->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->Ministries->setReturnValue('isAuthorized', true);
		$this->testController = $this->Ministries;
	}

	function endTest() {
		$this->Ministries->Session->destroy();
		unset($this->Ministries);
		$this->unloadSettings();
		ClassRegistry::flush();
	}

	function testBulkEdit() {
		$this->Ministries->Session->write('MultiSelect.test', array(
			'selected' => array(1,4,5),
			'search' => array()
		));
		$vars = $this->testAction('/ministries/bulk_edit/test', array(
			'data' => array(
				'Ministry' => array(
					'active' => 1,
					'campus_id' => 1,
					'parent_id' => 10,
					'move_campus' => 0,
					'move_ministry' => 0,
				)
			)
		));
		$ministries = $this->Ministries->Ministry->find('all', array(
			'conditions' => array(
				'Ministry.id' => array(1,4,5)
			)
		));
		$results = Set::extract('/Ministry/active', $ministries);
		sort($results);
		$expected = array(1,1,1);
		$this->assertEqual($results, $expected);

		$results = Set::extract('/Ministry/campus_id', $ministries);
		sort($results);
		$expected = array(1,1,2);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/ministries/bulk_edit/test', array(
			'data' => array(
				'Ministry' => array(
					'active' => 1,
					'campus_id' => 1,
					'parent_id' => 10,
					'move_campus' => 1,
					'move_ministry' => 0,
				)
			)
		));
		$ministries = $this->Ministries->Ministry->find('all', array(
			'conditions' => array(
				'Ministry.id' => array(1,4,5)
			)
		));
		$results = Set::extract('/Ministry/campus_id', $ministries);
		sort($results);
		$expected = array(1,1,1);
		$this->assertEqual($results, $expected);
	}

	function testIndex() {
		Core::read('notifications.ministry_content');
		$this->loadFixtures('Involvement', 'InvolvementsMinistry');
		$vars = $this->testAction('/ministries/index');
		$results = Set::extract('/Ministry[id=3]/../DisplayInvolvement/name', $vars['ministries']);
		sort($results);
		$expected = array(
			'Rock Climbing',
			'Team CORE'
		);
		$this->assertEqual($results, $expected);

		$results = Set::extract('/Ministry[id=4]/../DisplayInvolvement/name', $vars['ministries']);
		sort($results);
		$expected = array(
			'Third Wednesday'
		);
		$this->assertEqual($results, $expected);
	}

	function testAdd() {
		$data = array(
			'Ministry' => array(
				'name' => 'New Root Ministry',
				'description' => 'Description',
				'parent_id' => 1,
				'campus_id' => 1,
				'private' => 1,
				'active' => 1
			)
		);

		$vars = $this->testAction('/ministries/add/Campus:1', array(
			'return' => 'vars',
			'data' => $data
		));
		$ministry = $this->Ministries->Ministry->read(null, $this->Ministries->Ministry->id);
		$result = $ministry['Ministry']['name'];
		$this->assertEqual($result, 'New Root Ministry');

		$results = $vars['ministries'];
		ksort($results);
		$expected = array(
			1 => 'Communications',
			2 => 'Alpha'
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
			2 => 'Alpha'
		);
		$this->assertEqual($result, $expected);

		$this->Ministries->Ministry->id = 1;
		$result = $this->Ministries->Ministry->field('name');
		$this->assertEqual($result, 'New name');
	}

	function testToggleActivity() {
		$this->testAction('/ministries/toggle_activity/1/Ministry:3');
		$this->Ministries->Ministry->id = 3;
		$this->assertEqual($this->Ministries->Session->read('Message.flash.element'), 'flash'.DS.'failure');

		$data = array(
			'Leader' => array(
				'user_id' => 1,
				'model' => 'Ministry',
				'model_id' => 3
			)
		);
		$this->Ministries->Ministry->Leader->save($data);
		$this->testAction('/ministries/toggle_activity/1/Ministry:3');
		$this->Ministries->Ministry->id = 3;
		$this->assertEqual($this->Ministries->Ministry->field('active'), 1);
		$this->assertEqual($this->Ministries->Session->read('Message.flash.element'), 'flash'.DS.'success');
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
		$this->testAction('/ministries/revise/0/Ministry:1');
		$result = $this->Ministries->Ministry->RevisionModel->find('all');
		$this->assertFalse($result);
		$this->Ministries->Ministry->id = 1;
		$result = $this->Ministries->Ministry->field('name');
		$this->assertEqual($result, 'Communications');

		$this->Ministries->Ministry->RevisionModel->save($data);
		$this->testAction('/ministries/revise/1/Ministry:1');
		$result = $this->Ministries->Ministry->RevisionModel->find('all');
		$this->assertFalse($result);
		$this->Ministries->Ministry->id = 1;
		$result = $this->Ministries->Ministry->field('name');
		$this->assertEqual($result, 'New name');
	}

	function testDelete() {
		$this->testAction('/ministries/delete/1');
		$this->assertFalse($this->Ministries->Ministry->read(null, 1));
	}

}
?>