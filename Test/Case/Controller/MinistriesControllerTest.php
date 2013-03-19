<?php
/* Ministries Test cases generated on: 2010-07-16 08:07:10 : 1279292770 */
App::uses('CoreTestCase', 'Lib');
App::uses('QueueEmailComponent', 'QueueEmail.Controller/Component');
App::uses('MinistriesController', 'Controller');

Mock::generatePartial('QueueEmailComponent', 'MockMinistriesQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('MinistriesController', 'TestMinistriesController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class MinistriesControllerTestCase extends CoreTestCase {

	public function startTest($method) {
		parent::startTest($method);
		// necessary fixtures
		$this->loadSettings();
		$this->loadFixtures('Ministry', 'MinistriesRev');
		$this->Ministries =& new TestMinistriesController();
		$this->Ministries->__construct();
		$this->Ministries->constructClasses();
		$this->Ministries->Notifier->QueueEmail = new MockMinistriesQueueEmailComponent();
		$this->Ministries->Notifier->QueueEmail->enabled = true;
		$this->Ministries->Notifier->QueueEmail->initialize($this->Ministries);
		$this->Ministries->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Ministries->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->Ministries->setReturnValue('isAuthorized', true);
		$this->testController = $this->Ministries;
	}

	public function endTest() {
		$this->Ministries->Session->destroy();
		unset($this->Ministries);
		$this->unloadSettings();
		ClassRegistry::flush();
	}

	public function testIndex() {
		$this->loadFixtures('Group');

		$vars = $this->testAction('/ministries/index/Campus:2');
		$results = Set::extract('/Ministry/id', $vars['ministries']);
		sort($results);
		$this->assertEqual($results, array());

		$results = $this->Ministries->data;
		$expected = array(
			'Ministry' => array(
				'inactive' => false,
				'private' => false
			)
		);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/ministries/index/Campus:1');
		$results = Set::extract('/Ministry/id', $vars['ministries']);
		sort($results);
		$this->assertEqual($results, array(1, 2));

		$vars = $this->testAction('/ministries/index/Campus:1', array(
			'data' => array(
				'Ministry' => array(
					'inactive' => false,
					'private' => false
				)
			)
		));
		$results = Set::extract('/Ministry/id', $vars['ministries']);
		sort($results);
		$this->assertEqual($results, array(1, 2));

		$vars = $this->testAction('/ministries/index/Campus:1', array(
			'data' => array(
				'Ministry' => array(
					'private' => true,
					'inactive' => true
				)
			)
		));
		$results = Set::extract('/Ministry/id', $vars['ministries']);
		sort($results);
		$this->assertEqual($results, array(1, 2, 3));

		$this->su(array('Group' => 8));

		$vars = $this->testAction('/ministries/index/Campus:1');
		$results = Set::extract('/Ministry/id', $vars['ministries']);
		sort($results);
		$this->assertEqual($results, array(1, 2));

		$vars = $this->testAction('/ministries/index/Campus:2');
		$results = Set::extract('/Ministry/id', $vars['ministries']);
		sort($results);
		$this->assertEqual($results, array());

		$results = $this->Ministries->data;
		$expected = array(
			'Ministry' => array(
				'inactive' => false,
				'private' => false
			)
		);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/ministries/index/Ministry:1', array(
			'data' => array(
				'Ministry' => array(
					'private' => true,
					'inactive' => true
				)
			)
		));
		$results = Set::extract('/Ministry/id', $vars['ministries']);
		sort($results);
		$this->assertEqual($results, array(4));


		$this->Ministries->Ministry->save(array(
			'id' => 1,
			'private' => 1
		));
		$vars = $this->testAction('/ministries/index/Campus:1', array(
			'data' => array(
				'Ministry' => array(
					'private' => true,
					'inactive' => false
				)
			)
		));
		$results = Set::extract('/Ministry/id', $vars['ministries']);
		sort($results);
		$this->assertEqual($results, array(1, 2));
	}

	public function testView() {
		$this->loadFixtures('Group');

		$vars = $this->testAction('/ministries/view/Ministry:3');
		$result = $vars['ministry']['Ministry']['id'];
		$this->assertEqual($result, 3);

		$this->su(array('Group' => array('id' => 8)));
		$vars = $this->testAction('/ministries/view/Ministry:3');

		$result = $vars['ministry']['Ministry']['id'];
		$this->assertEqual($result, 3);

		$this->assertTrue(empty($vars['ministry']['ChildMinistry']));
	}

	public function testBulkEdit() {
		$this->Ministries->Session->write('MultiSelect.test', array(
			'selected' => array(1,4,5),
			'search' => array()
		));
		$vars = $this->testAction('/ministries/bulk_edit/mstoken:test', array(
			'data' => array(
				'Ministry' => array(
					'active' => 1,
					'private' => '',
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
			),
			'order' => 'id ASC'
		));
		$results = Set::extract('/Ministry/active', $ministries);
		$expected = array(1,1,1);
		$this->assertEqual($results, $expected);

		$results = Set::extract('/Ministry/private', $ministries);
		$expected = array(0,0,1);
		$this->assertEqual($results, $expected);

		$results = Set::extract('/Ministry/campus_id', $ministries);
		$expected = array(1,1,2);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/ministries/bulk_edit/mstoken:test', array(
			'data' => array(
				'Ministry' => array(
					'active' => 1,
					'private' => '',
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
			),
			'order' => 'id ASC'
		));
		$results = Set::extract('/Ministry/campus_id', $ministries);
		$expected = array(1,1,1);
		$this->assertEqual($results, $expected);

		$results = Set::extract('/Ministry/parent_id', $ministries);
		$expected = array(0,0,0);
		$this->assertEqual($results, $expected);

		// put #5 back into campus 2 to test moving to a ministry under a different campus
		$this->Ministries->Ministry->save(array(
			'Ministry' => array(
				'id' => 5,
				'campus_id' => 2
			)
		));
		$vars = $this->testAction('/ministries/bulk_edit/mstoken:test', array(
			'data' => array(
				'Ministry' => array(
					'active' => 1,
					'private' => '',
					'campus_id' => 1,
					'parent_id' => 5,
					'move_campus' => 0,
					'move_ministry' => 1,
				)
			)
		));
		$ministries = $this->Ministries->Ministry->find('all', array(
			'conditions' => array(
				'Ministry.id' => array(1,4,5)
			),
			'order' => 'id ASC'
		));
		$results = Set::extract('/Ministry/campus_id', $ministries);
		$expected = array(2,2,2);
		$this->assertEqual($results, $expected);

		$results = Set::extract('/Ministry/parent_id', $ministries);
		$expected = array(5,5,null);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/ministries/bulk_edit/mstoken:test', array(
			'data' => array(
				'Ministry' => array(
					'active' => 1,
					'private' => '',
					'campus_id' => 1,
					'parent_id' => 5,
					'move_campus' => 1,
					'move_ministry' => 1,
				)
			)
		));
		$ministries = $this->Ministries->Ministry->find('all', array(
			'conditions' => array(
				'Ministry.id' => array(1,4,5)
			),
			'order' => 'id ASC'
		));
		$results = Set::extract('/Ministry/campus_id', $ministries);
		$expected = array(1,1,1);
		$this->assertEqual($results, $expected);

		$results = Set::extract('/Ministry/parent_id', $ministries);
		$expected = array(0,0,0);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/ministries/bulk_edit/mstoken:test', array(
			'data' => array(
				'Ministry' => array(
					'active' => 1,
					'private' => '',
					'campus_id' => 2,
					'parent_id' => 5,
					'move_campus' => 1,
					'move_ministry' => 0,
				)
			)
		));
		$ministries = $this->Ministries->Ministry->find('all', array(
			'conditions' => array(
				'Ministry.id' => array(1,4,5)
			),
			'order' => 'id ASC'
		));
		$results = Set::extract('/Ministry/campus_id', $ministries);
		$expected = array(2,2,2);
		$this->assertEqual($results, $expected);
	}

	public function testAdd() {
		$countBefore = $this->Ministries->Ministry->find('count');
		$vars = $this->testAction('/ministries/add/Campus:1');
		$this->assertNull($this->Ministries->Session->read('Message'));
		$countAfter = $this->Ministries->Ministry->find('count');
		$this->assertEqual($countBefore - $countAfter, 0);

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

		$vars = $this->testAction('/ministries/add/Campus:2/Ministry:1');
		$this->assertEqual($vars['parentId'], 1);
		$this->assertEqual($this->testController->data['Ministry']['campus_id'], 1);
	}

	public function testEdit() {
		$this->loadFixtures('User');

		$data = $this->Ministries->Ministry->read(null, 1);
		$data['Ministry']['name'] = 'New name';

		$this->Ministries->Ministry->Behaviors->enable('Confirm');
		$this->Ministries->setReturnValueAt(0, 'isAuthorized', true);
		$this->Ministries->setReturnValueAt(1, 'isAuthorized', true);

		$notificationsBefore = ClassRegistry::init('Notification')->find('count');
		$vars = $this->testAction('/ministries/edit/Ministry:1', array(
			'return' => 'vars',
			'data' => $data
		));
		$notificationsAfter = ClassRegistry::init('Notification')->find('count');

		$result = $vars['ministries'];
		$expected = array(
			2 => 'Alpha'
		);
		$this->assertEqual($result, $expected);

		$result = $notificationsAfter-$notificationsBefore;
		$expected = 0;
		$this->assertEqual($result, $expected);

		$this->Ministries->Ministry->id = 1;
		$result = $this->Ministries->Ministry->field('name');
		$this->assertEqual($result, 'New name');

		$this->Ministries->Ministry->Behaviors->enable('Confirm');
		$this->Ministries->setReturnValueAt(2, 'isAuthorized', true);
		$this->Ministries->setReturnValueAt(3, 'isAuthorized', false);

		$data = $this->Ministries->Ministry->read(null, 1);
		$data['Ministry']['name'] = 'This change should end up pending';

		$notificationsBefore = ClassRegistry::init('Notification')->find('count');
		$vars = $this->testAction('/ministries/edit/Ministry:1', array(
			'return' => 'vars',
			'data' => $data
		));
		$notificationsAfter = ClassRegistry::init('Notification')->find('count');

		$result = $this->Ministries->Ministry->RevisionModel->field('name');
		$expected = 'This change should end up pending';
		$this->assertEqual($result, $expected);

		$result = $notificationsAfter-$notificationsBefore;
		$expected = 1;
		$this->assertEqual($result, $expected);
	}

	public function testToggleActivity() {
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

	public function testHistory() {
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

	public function testRevise() {
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

	public function testDelete() {
		$this->testAction('/ministries/delete/Ministry:1');
		$this->assertFalse($this->Ministries->Ministry->read(null, 1));
	}

}
