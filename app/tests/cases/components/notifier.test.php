<?php

App::import('Model', 'Notification');
App::import('Component', 'Notifier');

class TestNotifierController extends Controller {}

class NotifierTestCase extends CakeTestCase {

	var $fixtures = array(
		'app.notification','app.ministries_rev', 'app.involvements_rev',
		'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category',
		'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement',
		'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date',
		'app.payment_option', 'app.question', 'app.roster', 'app.role', 'app.roster_status',
		'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type',
		'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member',
		'app.household', 'app.publication', 'app.publications_user', 'app.log'
	);

	function startTest() {
		$this->Notifier = new NotifierComponent();
		$this->Controller = new TestNotifierController();
		$this->Notifier->initialize($this->Controller, array());
		$this->Notification = ClassRegistry::init('Notification');
		$this->Notifier->notification = $this->Notification;
	}

	function endTest() {
		unset($this->Notifier);
		unset($this->Notification);
		unset($this->Controller);
		ClassRegistry::flush();
	}

	function testNotify() {
		$this->Controller->set('name', 'Jeremy');
		$this->Controller->set('type', 'leading');
		$this->Controller->set('itemType', 'Team');
		$this->Controller->set('itemName', 'Core Developers');

		$this->assertTrue($this->Notifier->notify(1, 'leaders_add'));
		$this->assertFalse($this->Notifier->notify(1, 'nonexistantnotification'));
	}

	function test_save() {
		$dateReg = 'preg:/([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?/';

		$data = array(
			$this->Notifier->foreignKey => 1,
			$this->Notifier->contentField => 'A notification'
		);

		unset($this->Notifier->notification);
		$this->Notifier->_save($data);
		$this->assertError('Notifier::_save() : Notification model does not exist.');
		$this->Notifier->notification = $this->Notification;

		$this->Notifier->_save($data);
		$this->Notification->recursive = -1;
		$result = $this->Notification->read(array('user_id', 'body', 'read', 'type'));
		$expected = array(
			$this->Notifier->notification->name => array(
				'user_id' => 1,
				'body' => 'A notification',
				'read' => false,
				'type' => null
			)
		);
		$this->assertEqual($result, $expected);
		
		$this->Notifier->saveData = array(
			'type' => 'invitation'
		);
		$this->Notifier->_save($data);
		$this->Notification->recursive = -1;
		$result = $this->Notification->read(array('user_id', 'body', 'read', 'type'));
		$expected = array(
			$this->Notifier->notification->name => array(
				'user_id' => 1,
				'body' => 'A notification',
				'read' => false,
				'type' => 'invitation'
			)
		);
		$this->assertEqual($result, $expected);
	}

	function test_render() {
		$this->Controller->set('name', 'Jeremy');
		$this->Controller->set('type', 'leading');
		$this->Controller->set('itemType', 'Team');
		$this->Controller->set('itemName', 'Core Developers');

		$expected = 'Jeremy is now leading the team Core Developers.';
		$result = $this->Notifier->_render('leaders_add');
		$this->assertEqual($result, $expected);

		$this->assertFalse($this->Notifier->_render('nonexistantnotification'));
	}

}



?>
