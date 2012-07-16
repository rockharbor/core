<?php
/* MergeRequests Test cases generated on: 2010-07-14 13:07:43 : 1279138963 */
App::import('Controller', 'MergeRequests');
App::import('Model', array('User', 'Profile'));
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail.QueueEmail'));

Mock::generatePartial('QueueEmailComponent', 'MockMergeRequestsQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('MergeRequestsController', 'TestMergeRequestsController', array('isAuthorized', 'disableCache', 'render', 'redirect', '_stop', 'header', 'cakeError'));

class MergeRequestsControllerTestCase extends CoreTestCase {

	function startTest($method) {
		parent::startTest($method);
		$this->MergeRequests =& new TestMergeRequestsController();
		$this->MergeRequests->__construct();
		$this->MergeRequests->constructClasses();
		// necessary fixtures
		$this->loadFixtures('User', 'Profile', 'MergeRequest', 'Address');
		$this->MergeRequests->Notifier->QueueEmail = new MockMergeRequestsQueueEmailComponent();
		$this->MergeRequests->Notifier->QueueEmail->enabled = true;
		$this->MergeRequests->Notifier->QueueEmail->initialize($this->MergeRequests);
		$this->MergeRequests->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->MergeRequests->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->MergeRequests->setReturnValue('isAuthorized', true);
		$this->testController = $this->MergeRequests;;
	}

	function endTest() {
		unset($this->MergeRequests);		
		ClassRegistry::flush();
	}

	function testIndex() {
		$vars = $this->testAction('/merge_requests/index/model:User', array(
			'return' => 'vars'
		));
		$results = Set::extract('/MergeRequest', $vars['requests']);
		$expected = array(
			array(
				'MergeRequest' => array(
					'id' => 1,
					'model' => 'User',
					'model_id' => 3,
					'merge_id' => 2,
					'requester_id' => 2,
					'created' => '2010-07-15 00:00:00',
					'modified' => '2010-07-15 00:00:00',
				)
			)
		);
		$this->assertEqual($results, $expected);
		$this->assertEqual($vars['requests'][0]['NewModel']['id'], 3);
		$this->assertEqual($vars['requests'][0]['OriginalModel']['id'], 2);
	}

	function testMerge() {
		$this->loadFixtures('HouseholdMember', 'Household');
		
		$this->Profile =& ClassRegistry::init('Profile');
		$this->User =& ClassRegistry::init('User');
		$vars = $this->testAction('/merge_requests/merge/1');

		$results = $this->MergeRequests->MergeRequest->find('all');
		$this->assertEqual($results, array());

		$results = $this->User->find('count');
		$this->assertEqual($results, 4);

		$results = $this->Profile->find('count');
		$this->assertEqual($results, 4);

		$this->User->contain(array('Profile'));
		$user = $this->User->read(null, 2);

		$result = $user['User']['username'];
		$this->assertEqual($result, 'rickyrockharborjr');

		$result = $user['Profile']['primary_email'];
		$this->assertEqual($result, 'rickyjr@rockharbor.org');
		
		// remember that the user was merged so he has a new username
		$this->assertEqual($vars['user']['User']['username'], 'rickyrockharborjr');
		$this->assertEqual($vars['user']['User']['id'], 2);
	}

	function testDelete() {
		$this->Profile =& ClassRegistry::init('Profile');
		$this->User =& ClassRegistry::init('User');
		$this->testAction('/merge_requests/delete/1');

		$this->assertFalse($this->MergeRequests->MergeRequest->read(null, 1));
		
		$results = $this->User->read(null, 3);
		$this->assertFalse($results);

		$results = $this->User->read(null, 2);
		$this->assertTrue(!empty($results));
		
		$result = $this->Profile->find('count');
		$this->assertEqual($result, 4);

		$result = $this->User->find('count');
		$this->assertEqual($result, 4);
	}
	
	function testIgnore() {
		$Profile =& ClassRegistry::init('Profile');
		$User =& ClassRegistry::init('User');
		$vars = $this->testAction('/merge_requests/delete/1/1');

		$this->assertFalse($this->MergeRequests->MergeRequest->read(null, 1));
		
		// activated the new user
		$results = $User->read(null, 2);
		$this->assertEqual($results['User']['active'], 1);

		// original user remains untouched
		$results = $User->read(null, 3);
		$this->assertTrue(!empty($results));
		
		$this->assertEqual($vars['user']['User']['username'], 'rickyrockharborjr');
	}

}
