<?php
/* Publications Test cases generated on: 2010-07-19 10:07:29 : 1279558889 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail'));
App::import('Controller', 'Publications');

Mock::generate('QueueEmailComponent');
Mock::generatePartial('PublicationsController', 'TestPublicationsController', array('isAuthorized', 'render', 'redirect', '_stop', 'header'));

class PublicationsControllerTestCase extends CoreTestCase {
	var $fixtures = array('app.notification', 'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.roster', 'app.role', 'app.roster_status', 'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member', 'app.household', 'app.publication', 'app.publications_user', 'app.log', 'app.app_setting', 'app.alert', 'app.alerts_user', 'app.aro', 'app.aco', 'app.aros_aco', 'app.ministries_rev', 'app.involvements_rev', 'app.error', 'app.log');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		$this->loadFixtures('Publication', 'PublicationsUser', 'User', 'Group');
		$this->Publications =& new TestPublicationsController();
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