<?php
/* Publications Test cases generated on: 2010-07-19 10:07:29 : 1279558889 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail'));
App::import('Controller', 'Publications');

Mock::generate('QueueEmailComponent');
Mock::generatePartial('PublicationsController', 'TestPublicationsController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class PublicationsControllerTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('Publication', 'PublicationsUser', 'User', 'Group');
		$this->Publications =& new TestPublicationsController();
		$this->Publications->__construct();
		$this->Publications->constructClasses();		
		$this->Publications->Component->initialize($this->Publications);
		$this->Publications->QueueEmail = new MockQueueEmailComponent();
		$this->Publications->setReturnValue('isAuthorized', true);
		$this->Publications->QueueEmail->setReturnValue('send', true);
		$this->testController = $this->Publications;
	}

	function endTest() {
		unset($this->Publications);
		ClassRegistry::flush();
	}

	function testSubscription() {
		$vars = $this->testAction('/publications/subscriptions/User:1');
		sort($vars['subscriptions']);
		$results = $vars['subscriptions'];
		$expected = array(1,2);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/publications/subscriptions/User:1');
		$results = Set::extract('/Publication/name', $vars['publications']);
		$expected = array('ebulletin', 'Family Ministry Update');
		$this->assertEqual($results, $expected);
	}

	function testToggleSubscribe() {
		$this->testAction('/publications/toggle_subscribe/1/0/User:1');
		$vars = $this->testAction('/publications/subscriptions/User:1');
		sort($vars['subscriptions']);
		$results = $vars['subscriptions'];
		$expected = array(2);
		$this->assertEqual($results, $expected);

		$this->testAction('/publications/toggle_subscribe/1/1/User:1');
		$vars = $this->testAction('/publications/subscriptions/User:1');
		sort($vars['subscriptions']);
		$results = $vars['subscriptions'];
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);

		// try duplicating subscription
		$this->testAction('/publications/toggle_subscribe/1/1/User:1');
		$vars = $this->testAction('/publications/subscriptions/User:1');
		sort($vars['subscriptions']);
		$results = $vars['subscriptions'];
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);

		$this->testAction('/publications/toggle_subscribe/1/1/User:2');
		$vars = $this->testAction('/publications/subscriptions/User:2');
		sort($vars['subscriptions']);
		$results = $vars['subscriptions'];
		$expected = array(1, 2);
		$this->assertEqual($results, $expected);
	}

}
?>