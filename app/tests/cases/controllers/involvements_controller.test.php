<?php
/* Involvements Test cases generated on: 2010-07-12 11:07:51 : 1278959751 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail', 'Notifier'));
App::import('Controller', 'Involvements');

Mock::generate('QueueEmailComponent');
Mock::generatePartial('NotifierComponent', 'MockNotifierComponent', array('_render'));
Mock::generatePartial('InvolvementsController', 'TestInvolvementsController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class InvolvementsControllerTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('Involvement', 'Roster', 'User', 'InvolvementType', 'Group', 'Ministry');
		$this->loadFixtures('InvolvementsRev', 'MinistriesRev', 'Leader');
		$this->Involvements =& new TestInvolvementsController();
		$this->Involvements->constructClasses();
		$this->Involvements->Notifier = new MockNotifierComponent();
		$this->Involvements->Notifier->setReturnValue('_render', 'Notification body text');
		$this->Involvements->QueueEmail = new MockQueueEmailComponent();
		$this->Involvements->QueueEmail->setReturnValue('send', true);
		$this->Involvements->setReturnValue('isAuthorized', true);
		$this->testController = $this->Involvements;
	}

	function endTest() {
		$this->Involvements->Session->destroy();
		unset($this->Involvements);
		ClassRegistry::flush();
	}

	function testInviteRoster() {
		$vars = $this->testAction('/involvements/invite_roster/1/Involvement:2');
		$invites = $this->Involvements->Involvement->Roster->User->Notification->find('all', array(
			'conditions' => array(
				'Notification.type' => 'invitation'
			)
		));
		$this->assertEqual(count($invites), 1);
	}

	function testInvite() {
		$vars = $this->testAction('/involvements/invite/1/Involvement:2');
		$invites = $this->Involvements->Involvement->Roster->User->Notification->find('all', array(
			'conditions' => array(
				'Notification.type' => 'invitation'
			)
		));
		$this->assertEqual(count($invites), 1);
	}

	function testAdd() {
		$data = array(
			'Involvement' => array(
				'ministry_id' => 4,
				'involvement_type_id' => 1,
				'name' => 'A test involvement',
				'description' => 'this is a test',
				'roster_limit' => null,
				'roster_visible' => 1,
				'group_id' => NULL,
				'signup' => 1,
				'take_payment' => 1,
				'offer_childcare' => 0,
				'active' => 1,
				'force_payment' => 0
			)
		);
		$this->testAction('/involvements/add', array(
			'data' => $data
		));
		$this->assertEqual($this->Involvements->Involvement->field('name'), 'A test involvement');
		$this->assertEqual($this->Involvements->Involvement->field('group_id'), 0);

		$data = array(
			'Involvement' => array(
				'ministry_id' => 4,
				'involvement_type_id' => 1,
				'name' => 'Another test involvement',
				'description' => 'Test using linked ministries',
				'roster_limit' => null,
				'roster_visible' => 1,
				'group_id' => NULL,
				'signup' => 0,
				'take_payment' => 0,
				'offer_childcare' => 0,
				'active' => 1,
				'force_payment' => 0
			),
			'DisplayMinistry' => array(
				'DisplayMinistry' => 1
			)
		);
		$this->testAction('/involvements/add', array(
			'data' => $data
		));
		$this->Involvements->Involvement->recursive = 1;
		$involvement = $this->Involvements->Involvement->read();
		$results = Set::extract('/DisplayMinistry/name', $involvement);
		$expected = array(
			'Communications'
		);
		$this->assertEqual($results, $expected);

		$this->Involvements->Involvement->Ministry->recursive = 1;
		$ministry = $this->Involvements->Involvement->Ministry->read(null, 1);
		$results = Set::extract('/DisplayInvolvement/name', $ministry);
		$expected = array(
			'Another test involvement'
		);
		$this->assertEqual($results, $expected);
	}

	function testEdit() {
		$data = $this->Involvements->Involvement->read(null, 1);
		$data['Involvement']['name'] = 'New name';
		
		$vars = $this->testAction('/involvements/edit/Involvement:1', array(
			'data' => $data
		));
		$this->Involvements->Involvement->id = 1;
		$this->assertEqual($this->Involvements->Involvement->field('name'), 'New name');
	}

	function testToggleActivityWithoutLeader() {
		$this->testAction('/involvements/toggle_activity/1/Involvement:2');
		$this->Involvements->Involvement->id = 2;
		$this->assertEqual($this->Involvements->Involvement->field('active'), 0);
		$this->assertEqual($this->Involvements->Session->read('Message.flash.element'), 'flash'.DS.'failure');

		$data = array(
			'Leader' => array(
				'user_id' => 1,
				'model' => 'Involvement',
				'model_id' => 2
			)
		);
		$this->Involvements->Involvement->Leader->save($data);
		$this->testAction('/involvements/toggle_activity/1/Involvement:2');
		$this->Involvements->Involvement->id = 2;
		$this->assertEqual($this->Involvements->Involvement->field('active'), 1);
		$this->assertEqual($this->Involvements->Session->read('Message.flash.element'), 'flash'.DS.'success');
	}

	function testToggleActivity() {
		$this->testAction('/involvements/toggle_activity/1/Involvement:3');
		$this->Involvements->Involvement->id = 3;
		$this->assertEqual($this->Involvements->Session->read('Message.flash.element'), 'flash'.DS.'failure');

		$data = array(
			'PaymentOption' => array(
				'involvement_id' => 3,
				'name' => 'pay for me!',
				'total' => 89,
				'deposit' => 54,
				'childcare' => NULL,
				'account_code' => '123456',
				'tax_deductible' => 1
			)
		);
		$this->Involvements->Involvement->PaymentOption->save($data);
		$this->testAction('/involvements/toggle_activity/1/Involvement:3');
		$this->Involvements->Involvement->id = 3;
		$this->assertEqual($this->Involvements->Involvement->field('active'), 1);
		$this->assertEqual($this->Involvements->Session->read('Message.flash.element'), 'flash'.DS.'success');
	}

	function testHistory() {
		$data = $this->Involvements->Involvement->read(null, 1);
		$data['Involvement']['name'] = 'New name';
		$this->Involvements->Involvement->save($data);

		$vars = $this->testAction('/involvements/history/Involvement:1', array(
			'return' => 'vars'
		));

		$result = $vars['revision']['Revision']['id'];
		$this->assertEqual($result, 1);

		$result = $vars['revision']['Revision']['name'];
		$this->assertEqual($result, 'New name');
	}

	function testRevise() {
		$data = $this->Involvements->Involvement->read(null, 1);
		$data['Involvement']['name'] = 'New name';
		$data = array(
			'Revision' => $data['Involvement']
		);		

		$this->Involvements->Involvement->RevisionModel->save($data);
		$this->Involvements->Involvement->id = 1;
		$this->testAction('/involvements/revise/0/Involvement:1');
		$result = $this->Involvements->Involvement->RevisionModel->find('all');
		$this->assertFalse($result);
		$result = $this->Involvements->Involvement->field('name');
		$this->assertEqual($result, 'CORE 2.0 testing');

		$this->Involvements->Involvement->RevisionModel->save($data);
		$this->Involvements->Involvement->id = 1;
		$this->testAction('/involvements/revise/1/Involvement:1');
		$result = $this->Involvements->Involvement->RevisionModel->find('all');
		$this->assertFalse($result);
		$result = $this->Involvements->Involvement->field('name');
		$this->assertEqual($result, 'New name');
	}

	function testDelete() {
		$this->testAction('/involvements/delete/1');
		$this->assertFalse($this->Involvements->Involvement->read(null, 1));
	}

}
?>