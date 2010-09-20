<?php
/* MergeRequests Test cases generated on: 2010-07-14 13:07:43 : 1279138963 */
App::import('Controller', 'MergeRequests');
App::import('Model', array('User', 'Profile'));
App::import('Lib', 'CoreTestCase');
App::import('Component', 'QueueEmail');

Mock::generate('QueueEmailComponent');
Mock::generatePartial('MergeRequestsController', 'TestMergeRequestsController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class MergeRequestsControllerTestCase extends CoreTestCase {

	function startTest() {
		$this->MergeRequests =& new TestMergeRequestsController();
		$this->MergeRequests->__construct();
		$this->MergeRequests->constructClasses();
		// necessary fixtures
		$this->loadFixtures('User', 'Profile', 'MergeRequest');
		$this->MergeRequests->Component->initialize($this->MergeRequests);
		$this->MergeRequests->QueueEmail = new MockQueueEmailComponent();
		$this->MergeRequests->setReturnValue('isAuthorized', true);
		$this->MergeRequests->QueueEmail->setReturnValue('send', true);
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
		$this->assertEqual($vars['requests'][0]['Source']['id'], 3);
		$this->assertEqual($vars['requests'][0]['Target']['id'], 2);
	}

	function testView() {
		$vars = $this->testAction('/merge_requests/view/1', array(
			'return' => 'vars'
		));
		$this->assertTrue(isset($vars['result']['Source']['Profile']));
	}

	function testMerge() {
		$this->Profile =& ClassRegistry::init('Profile');
		$this->User =& ClassRegistry::init('User');
		$this->testAction('/merge_requests/merge/1');

		$results = $this->MergeRequests->MergeRequest->find('all');
		$this->assertEqual($results, array());

		$results = $this->User->find('count');
		$this->assertEqual($results, 2);

		$results = $this->Profile->find('count');
		$this->assertEqual($results, 2);

		$this->User->contain(array('Profile'));
		$user = $this->User->read(null, 2);

		$result = $user['User']['username'];
		$this->assertEqual($result, 'rickyrockharbor');

		$result = $user['Profile']['primary_email'];
		$this->assertEqual($result, 'ricky@rockharbor.org');
	}

	function testDelete() {
		$this->Profile =& ClassRegistry::init('Profile');
		$this->User =& ClassRegistry::init('User');
		$this->testAction('/merge_requests/delete/1');

		$this->assertFalse($this->MergeRequests->MergeRequest->read(null, 1));

		$result = $this->Profile->find('count');
		$this->assertEqual($result, 2);

		$result = $this->User->find('count');
		$this->assertEqual($result, 2);
	}

}
?>