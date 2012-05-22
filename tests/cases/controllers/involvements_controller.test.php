<?php
/* Involvements Test cases generated on: 2010-07-12 11:07:51 : 1278959751 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail.QueueEmail'));
App::import('Controller', 'Involvements');

Mock::generatePartial('QueueEmailComponent', 'MockInvolvementsQueueEmailComponent', array('_smtp', '_mail'));

class TestInvolvementsController extends InvolvementsController {
	
	private $_authorized = array();
	
	private $_defaultAuth = false;
	
	function setAuthorized($action, $return) {
		if ($action === null) {
			$this->_defaultAuth = $return;
			return;
		}
		if (stripos($action, 'controllers') === false) {
			$action = 'controllers/'.trim($action, '/');
		}
		$this->_authorized[strtolower($action)] = $return;
	}
	
	function isAuthorized($action = null) {
		if (empty($action)) {
			$action = $this->Auth->action();
		}
		if (stripos($action, 'controllers') === false) {
			$action = 'controllers/'.trim($action, '/');
		}
		if (!isset($this->_authorized[strtolower($action)])) {
			return $this->_defaultAuth;
		}
		return $this->_authorized[strtolower($action)];
	}
	
}
Mock::generatePartial('TestInvolvementsController', 'MockTestInvolvementsController', array('disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class InvolvementsControllerTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Involvement', 'Roster', 'User', 'InvolvementType', 'Group', 'Ministry');
		$this->loadFixtures('MinistriesRev', 'Leader');
		$this->Involvements =& new MockTestInvolvementsController();
		$this->Involvements->__construct();
		$this->Involvements->constructClasses();
		$this->Involvements->Notifier->QueueEmail = new MockInvolvementsQueueEmailComponent();
		$this->Involvements->Notifier->QueueEmail->enabled = true;
		$this->Involvements->Notifier->QueueEmail->initialize($this->Involvements);
		$this->Involvements->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Involvements->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->testController = $this->Involvements;
		$this->Involvements->setAuthorized(null, true);
	}

	function endTest() {
		$this->Involvements->Session->destroy();
		unset($this->Involvements);
		ClassRegistry::flush();
	}
	
	function testView() {
		$this->loadSettings();
		
		$this->Involvements->Involvement->id = 1;
		$this->Involvements->Involvement->saveField('roster_limit', 1);

		// public, not registered
		$this->Involvements->setAuthorized('/rosters/index', false);
		$this->su(array(
			'User' => array('id' => 100),
			'Group' => array('id' => 8)
		));
		$vars = $this->testAction('/involvements/view/Involvement:1');
		$this->assertFalse($vars['inRoster']);
		$this->assertFalse($vars['canSeeRoster']);
		$this->assertTrue($vars['full']);
		
		// inactive, not registered
		$this->su(array(
			'User' => array('id' => 100),
			'Group' => array('id' => 8)
		));
		$vars = $this->testAction('/involvements/view/Involvement:2');
		$this->assertFalse($vars['inRoster']);
		$this->assertFalse($vars['canSeeRoster']);
		$this->assertFalse($vars['full']);
		
		// private, not registered, but admin
		$this->Involvements->setAuthorized('/rosters/index', true);
		$this->su(array(
			'User' => array('id' => 100),
			'Group' => array('id' => 1)
		));
		$vars = $this->testAction('/involvements/view/Involvement:3');
		$this->assertFalse($vars['inRoster']);
		$this->assertTrue($vars['canSeeRoster']);
		$this->assertTrue(isset($vars['full']));
		
		// private, not registered, but campus leader
		$this->Involvements->setAuthorized('/rosters/index', false);
		$this->su(array(
			'User' => array('id' => 1),
			'Group' => array('id' => 8)
		));
		$vars = $this->testAction('/involvements/view/Involvement:5');
		$this->assertFalse($vars['inRoster']);
		$this->assertTrue($vars['canSeeRoster']);
		$this->assertTrue(isset($vars['full']));
		
		// private, not registered, but ministry leader
		$Leader = ClassRegistry::init('Leader');
		$Leader->save(array(
			'user_id' => 101,
			'model' => 'Involvement',
			'model_id' => 1,
		));
		$this->su(array(
			'User' => array('id' => 101),
			'Group' => array('id' => 8)
		));
		$vars = $this->testAction('/involvements/view/Involvement:1');
		$this->assertFalse($vars['inRoster']);
		$this->assertTrue($vars['canSeeRoster']);
		$this->assertTrue(isset($vars['full']));
		
		// private, not registered, but involvement leader
		$Leader->save(array(
			'user_id' => 100,
			'model' => 'Involvement',
			'model_id' => 3,
		));
		$this->su(array(
			'User' => array('id' => 100),
			'Group' => array('id' => 8)
		));
		$vars = $this->testAction('/involvements/view/Involvement:3');
		$this->assertFalse($vars['inRoster']);
		$this->assertTrue($vars['canSeeRoster']);
		$this->assertTrue(isset($vars['full']));
		
		// private, but registered, but not confirmed
		$Roster = ClassRegistry::init('Roster');
		$Roster->save(array(
			'user_id' => 98,
			'involvement_id' => 3,
			'roster_status_id' => 2
		));
		$newId = $Roster->id;
		$this->su(array(
			'User' => array('id' => 98),
			'Group' => array('id' => 8)
		));
		$vars = $this->testAction('/involvements/view/Involvement:3');
		$this->assertTrue($vars['inRoster']);
		$this->assertFalse($vars['canSeeRoster']);
		$this->assertTrue(isset($vars['full']));
		
		// private, but registered and confirmed
		$Roster = ClassRegistry::init('Roster');
		$Roster->save(array(
			'id' => $newId,
			'roster_status_id' => 1
		));
		$this->su(array(
			'User' => array('id' => 98),
			'Group' => array('id' => 8)
		));
		$vars = $this->testAction('/involvements/view/Involvement:3');
		$this->assertTrue($vars['inRoster']);
		$this->assertTrue($vars['canSeeRoster']);
		$this->assertTrue(isset($vars['full']));
		
		// private, but registered and confirmed but roster invisible
		$this->Involvements->expectAt(0, 'cakeError', array('privateItem', '*'));
		$this->Involvements->Involvement->id = 3;
		$this->Involvements->Involvement->saveField('roster_visible', false);
		$this->su(array(
			'User' => array('id' => 98),
			'Group' => array('id' => 8)
		));
		$vars = $this->testAction('/involvements/view/Involvement:3');
		$this->assertTrue($vars['inRoster']);
		$this->assertFalse($vars['canSeeRoster']);
		$this->assertTrue(isset($vars['full']));
		
		// private, not registered
		$this->Involvements->expectAt(1, 'cakeError', array('privateItem', '*'));
		$this->su(array(
			'User' => array('id' => 97),
			'Group' => array('id' => 8)
		));
		$vars = $this->testAction('/involvements/view/Involvement:3');
		$this->assertFalse(isset($vars['full']));
		
		$this->unloadSettings();
	}
	
	function testIndex() {
		$this->loadSettings();
		$this->loadFixtures('InvolvementsMinistry', 'Date', 'InvolvementsMinistry');
		
		$vars = $this->testAction('/involvements/index/Ministry:1', array(
			'data' => array(
				'Involvement' => array(
					'inactive' => 1,
					'private' => 0,
					'previous' => 1
				)
			)
		));
		$results = Set::extract('/Involvement/id', $vars['involvements']);
		sort($results);
		$expected = array(4, 5);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/involvements/index/Ministry:1', array(
			'data' => array(
				'Involvement' => array(
					'inactive' => 0,
					'private' => 0,
					'previous' => 0
				)
			)
		));
		$results = Set::extract('/Involvement/id', $vars['involvements']);
		sort($results);
		$expected = array();
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/involvements/index/Ministry:4', array(
			'data' => array(
				'Involvement' => array(
					'inactive' => 1,
					'private' => 1,
					'previous' => 1
				)
			)
		));
		$results = Set::extract('/Involvement/id', $vars['involvements']);
		sort($results);
		$expected = array(1, 2, 3);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/involvements/index/Ministry:4', array(
			'data' => array(
				'Involvement' => array(
					'inactive' => 1,
					'private' => 0,
					'previous' => 1
				)
			)
		));
		$results = Set::extract('/Involvement/id', $vars['involvements']);
		sort($results);
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/involvements/index/Ministry:1', array(
			'data' => array(
				'Involvement' => array(
					'inactive' => 1,
					'private' => 1,
					'previous' => 1
				)
			)
		));
		$results = Set::extract('/Involvement/id', $vars['involvements']);
		sort($results);
		$expected = array(4, 5);
		$this->assertEqual($results, $expected);
		
		$vars = $this->testAction('/involvements/index/Ministry:1', array(
			'data' => array(
				'Involvement' => array(
					'inactive' => 1,
					'private' => 1,
					'previous' => 0
				)
			)
		));
		$results = Set::extract('/Involvement/id', $vars['involvements']);
		sort($results);
		$expected = array();
		$this->assertEqual($results, $expected);
		
		$this->unloadSettings();
	}

	function testInviteRoster() {
		$this->loadFixtures('PaymentOption');
		
		$this->Involvements->Session->write('MultiSelect.test', array(
			'selected' => array(1),
			'search' => array()
		));
		$notificationCountBefore = $this->Involvements->Involvement->Roster->User->Notification->find('count');
		$countBefore = $this->Involvements->Involvement->Roster->find('count');
		$vars = $this->testAction('/involvements/invite_roster/test/3/Involvement:1');
		$invites = $this->Involvements->Involvement->Roster->User->Invitation->find('all');
		$this->assertEqual(count($invites), 2);
		$countNow = $this->Involvements->Involvement->Roster->find('count');
		// they were both already signed up, just their roster changed to invited
		$this->assertEqual($countBefore, $countNow);
		$notificationCountAfter = $this->Involvements->Involvement->Roster->User->Notification->find('count');
		// one for each leader for each roster
		$this->assertEqual($notificationCountAfter-$notificationCountBefore, 1);
		
		$newest = $this->Involvements->Involvement->Roster->read();
		$this->assertEqual($newest['Roster']['payment_option_id'], 1);
		
		$this->Involvements->Session->write('MultiSelect.test', array(
			'selected' => array(1, 3),
			'search' => array()
		));
		$notificationCountBefore = $this->Involvements->Involvement->Roster->User->Notification->find('count');
		$vars = $this->testAction('/involvements/invite_roster/test/3/Involvement:1');
		$notificationCountAfter = $this->Involvements->Involvement->Roster->User->Notification->find('count');
		// one for each leader for each roster
		$this->assertEqual($notificationCountAfter-$notificationCountBefore, 2);
	}

	function testInviteUser() {
		$this->loadFixtures('PaymentOption', 'Leader');
		
		$this->Involvements->Session->write('MultiSelect.test', array(
			'selected' => array(1, 2),
			'search' => array()
		));
		$countBefore = $this->Involvements->Involvement->Roster->find('count');
		$notificationCountBefore = $this->Involvements->Involvement->Roster->User->Notification->find('count');
		$vars = $this->testAction('/involvements/invite/test/3/Involvement:1');
		$invites = $this->Involvements->Involvement->Roster->User->Invitation->find('all');
		$this->assertEqual(count($invites), 2);
		$countNow = $this->Involvements->Involvement->Roster->find('count');
		// one already existed, one was invited
		$this->assertEqual($countBefore+1, $countNow);
		$notificationCountAfter = $this->Involvements->Involvement->Roster->User->Notification->find('count');
		// one for each leader for each user
		$this->assertEqual($notificationCountAfter-$notificationCountBefore, 2);
		
		$newest = $this->Involvements->Involvement->Roster->read();
		$this->assertEqual($newest['Roster']['payment_option_id'], 1);
		
		$this->Involvements->Session->write('MultiSelect.test', array(
			'selected' => array(4, 5),
			'search' => array()
		));
		$countBefore = $this->Involvements->Involvement->Roster->find('count');
		$notificationCountBefore = $this->Involvements->Involvement->Roster->User->Notification->find('count');
		$vars = $this->testAction('/involvements/invite/test/1/Involvement:1');
		$countNow = $this->Involvements->Involvement->Roster->find('count');
		$this->assertEqual($countNow - $countBefore, 2);
		$notificationCountAfter = $this->Involvements->Involvement->Roster->User->Notification->find('count');
		// one for each leader for each user and one for each user (one is inactive, so only one user is notified)
		$this->assertEqual($notificationCountAfter-$notificationCountBefore, 3);
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
				'private' => NULL,
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

		$data = array(
			'Involvement' => array(
				'ministry_id' => 4,
				'involvement_type_id' => 1,
				'name' => 'Another test involvement',
				'description' => 'Test using linked ministries',
				'roster_limit' => null,
				'roster_visible' => 1,
				'private' => NULL,
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

	function testDelete() {
		$this->testAction('/involvements/delete/Involvement:1');
		$this->assertFalse($this->Involvements->Involvement->read(null, 1));
	}

}
?>