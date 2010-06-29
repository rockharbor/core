<?php
/* Notifications Test cases generated on: 2010-06-28 09:06:37 : 1277744317*/
App::import('Controller', 'Notifications');

class TestNotificationsController extends NotificationsController {
	var $autoRender = false;

	function render($action = null, $layout = null, $file = null) {
		$this->renderedAction = $action;
	}

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}

	function _stop($status = 0) {
		$this->stopped = $status;
	}
}

class NotificationsControllerTestCase extends CakeTestCase {
	var $fixtures = array(
		'app.notification',
		'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category',
		'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement',
		'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date',
		'app.payment_option', 'app.question', 'app.roster', 'app.role', 'app.roster_status',
		'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type',
		'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member',
		'app.household', 'app.publication', 'app.publications_user', 'app.log', 'app.app_setting'
	);

	function _prepareAction($action = '') {
		$this->Notifications->params = Router::parse($action);
		$this->Notifications->passedArgs = array_merge($this->Notifications->params['named'], $this->Notifications->params['pass']);
		$this->Notifications->params['url'] = $this->Notifications->params;
		$this->Notifications->beforeFilter();
	}

	function startTest() {
		$this->Notifications =& new TestNotificationsController();
		$this->Notifications->constructClasses();
		$this->Notifications->Component->initialize($this->Notifications);
	}

	function endTest() {
		unset($this->Notifications);
		ClassRegistry::flush();
	}

	function testIndex() {
		$this->_prepareAction('/notifications/index/User:1');
		$this->Notifications->index();
		$expected = array(
			array(
				'Notification' => array(
					'id' => 1,
					'user_id' => 1,
					'created' => '2010-06-24 14:37:38',
					'modified' => '2010-06-24 14:37:38',
					'read' => 1,
					'type' => 'invitation',
					'body' => 'You have been invited somewhere.'
				)
			),
			array(
				'Notification' => array(
					'id' => 2,
					'user_id' => 1,
					'created' => '2010-06-04 10:24:49',
					'modified' => '2010-06-24 10:21:54',
					'read' => 1,
					'type' => 'default',
					'body' => 'Jeremy Harris is now managing the campus Fischer.'
				)
			)
		);
		$this->assertEqual($this->Notifications->viewVars['notifications'], $expected);
	}

	function testIndexFiltered() {
		$this->_prepareAction('/notifications/index/invitation/User:1');
		$this->Notifications->index('invitation');
		$expected = array(
			array(
				'Notification' => array(
					'id' => 1,
					'user_id' => 1,
					'created' => '2010-06-24 14:37:38',
					'modified' => '2010-06-24 14:37:38',
					'read' => 1,
					'type' => 'invitation',
					'body' => 'You have been invited somewhere.'
				)
			)
		);
		$this->assertEqual($this->Notifications->viewVars['notifications'], $expected);
	}

	function testRead() {

	}

	function testDelete() {

	}

}
?>