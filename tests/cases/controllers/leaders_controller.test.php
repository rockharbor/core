<?php
/* Leaders Test cases generated on: 2010-07-14 12:07:47 : 1279136267 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail.QueueEmail'));
App::import('Controller', array('InvolvementLeaders', 'MinistryLeaders', 'CampusLeaders'));

Mock::generatePartial('QueueEmailComponent', 'MockLeadersQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('InvolvementLeadersController', 'MockLeadersInvolvementLeadersController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));
Mock::generatePartial('MinistryLeadersController', 'MockLeadersMinistryLeadersController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));
Mock::generatePartial('CampusLeadersController', 'MockLeadersCampusLeadersController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class LeadersControllerTestCase extends CoreTestCase {

	function _setLeaderController($name = 'Involvement') {
		if (class_exists('MockLeaders'.$name.'LeadersController')) {
			$className = 'MockLeaders'.$name.'LeadersController';
			$this->Leaders =& new $className;
			$this->Leaders->__construct();
			$this->Leaders->constructClasses();
			$this->Leaders->Notifier->QueueEmail = new MockLeadersQueueEmailComponent();
			$this->Leaders->Notifier->QueueEmail->enabled = true;
			$this->Leaders->Notifier->QueueEmail->initialize($this->Leaders);
			$this->Leaders->Notifier->QueueEmail->setReturnValue('_smtp', true);
			$this->Leaders->Notifier->QueueEmail->setReturnValue('_mail', true);
			$this->testController = $this->Leaders;
		}
	}

	function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Leader', 'User', 'Profile', 'Involvement', 'Notification', 'Group', 'Ministry', 'Campus');
		$this->_setLeaderController();
	}

	function endTest() {
		unset($this->Leaders);
		ClassRegistry::flush();
	}

	function testIndex() {
		$vars = $this->testAction('involvement_leaders/index/Involvement:1', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Leader', $vars['leaders']);
		$expected = array(
			array(
				'Leader' => array(
					'id' => 2,
					'user_id' => 1,
					'model' => 'Involvement',
					'model_id' => 1,
					'created' => '2010-04-09 07:28:57',
					'modified' => '2010-04-09 07:28:57'
				)
			)
		);
		$this->assertEqual($results, $expected);
	}

	function testAdd() {
		$this->Leaders->Session->write('MultiSelect.test', array(
			'selected' => array(1, 2),
			'search' => array()
		));
		
		$notificationsBefore =  $this->Leaders->Leader->User->Notification->find('count');
		$vars = $this->testAction('/involvement_leaders/add/test/Involvement:1/model:Involvement', array(
			'return' => 'vars'
		));

		$notificationsAfter = $this->Leaders->Leader->User->Notification->find('count');
		// 2 for the users, 2x1 leader
		$this->assertEqual($notificationsAfter-$notificationsBefore, 4);
		
		$this->assertTrue($this->Leaders->Leader->hasAny(array(
			'user_id' => 1,
			'model' => 'Involvement',
			'model_id' => 1
		)));
		$this->assertTrue($this->Leaders->Leader->hasAny(array(
			'user_id' => 2,
			'model' => 'Involvement',
			'model_id' => 1
		)));
	}

	function testDelete() {
		$notificationsBefore =  $this->Leaders->Leader->User->Notification->find('count');
		$vars = $this->testAction('/involvement_leaders/delete/Involvement:1/User:1');
		$notificationsAfter = $this->Leaders->Leader->User->Notification->find('count');
		// can't remove the only leader
		$this->assertEqual($notificationsAfter-$notificationsBefore, 0);
		$this->assertEqual($this->Leaders->Session->read('Message.flash.element'), 'flash'.DS.'failure');
		
		$this->_setLeaderController('Ministry');
		$notificationsBefore =  $this->Leaders->Leader->User->Notification->find('count');
		$vars = $this->testAction('/ministry_leaders/delete/Ministry:4/User:1');
		$notificationsAfter = $this->Leaders->Leader->User->Notification->find('count');
		$results = $this->Leaders->Leader->User->Notification->find('count');
		// 1 for user, 1x1 leader left
		$this->assertEqual($notificationsAfter-$notificationsBefore, 2);
		$this->assertEqual($this->Leaders->Session->read('Message.flash.element'), 'flash'.DS.'success');
	}

}
