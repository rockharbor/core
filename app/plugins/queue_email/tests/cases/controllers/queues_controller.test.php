<?php
/* Queues Test cases generated on: 2010-09-17 10:09:29 : 1284744509*/
App::import('Controller', 'QueueEmail.Queues');

class TestQueuesController extends QueuesController {
	var $autoRender = false;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class QueuesControllerTestCase extends CakeTestCase {
	var $fixtures = array('plugin.queue_email.queue');

	function startTest() {
		$this->Queues =& new TestQueuesController();
		$this->Queues->constructClasses();
		$this->Queues->Component->initialize($this->Queues);
	}

	function endTest() {
		unset($this->Queues);
		ClassRegistry::flush();
	}

	function testIndex() {
		$this->Queues->params['url']['url'] = 'queues/index';
		$this->Queues->index();
		$results = $this->Queues->viewVars['queues'];
		$expected = array(
			array(
				'Queue' => array(
					'id' => 1,
					'to' => 'test@test.com',
					'cc' => null,
					'bcc' => null,
					'from' => 'test@test.com',
					'subject' => 'Mail',
					'delivery' => null,
					'smtp_options' => null,
					'message' => null,
					'header' => null,
					'created' => '2010-09-20 00:00:01',
					'modified' => '2010-09-20 00:00:01',
				)
			),
			array(
				'Queue' => array(
					'id' => 2,
					'to' => 'test@test.com',
					'cc' => null,
					'bcc' => null,
					'from' => 'test@test.com',
					'subject' => 'Mail',
					'delivery' => 'smtp',
					'smtp_options' => ARRAY(
						'port' => 25,
						'host' => 'example.smtp.server',
						'username' => 'username',
						'password' => 'password',
					),
					'message' => null,
					'header' => null,
					'created' => '2010-09-20 00:00:01',
					'modified' => '2010-09-20 00:00:01',
				)
			),
			array(
				'Queue' => array(
					'id' => 3,
					'to' => 'test@test.com',
					'cc' => null,
					'bcc' => null,
					'from' => 'test@test.com',
					'subject' => 'Mail',
					'delivery' => null,
					'smtp_options' => null,
					'message' => null,
					'header' => null,
					'created' => '2010-09-20 00:00:01',
					'modified' => '2010-09-20 00:00:01',
				)
			),
			array(
				'Queue' => array(
					'id' => 4,
					'to' => 'test@test.com',
					'cc' => null,
					'bcc' => null,
					'from' => 'test@test.com',
					'subject' => 'Mail',
					'delivery' => null,
					'smtp_options' => null,
					'message' => null,
					'header' => null,
					'created' => '2010-09-20 00:00:01',
					'modified' => '2010-09-20 00:00:01',
				)
			),
		);
		$this->assertEqual($results, $expected);
	}

	function testView() {
		$this->Queues->params['url']['url'] = 'queues/view';
		$this->Queues->view(2);
		$results = $this->Queues->viewVars['queue'];
		$expected = array(
			'Queue' => array(
				'id' => 2,
				'to' => 'test@test.com',
				'cc' => null,
				'bcc' => null,
				'from' => 'test@test.com',
				'subject' => 'Mail',
				'delivery' => 'smtp',
				'smtp_options' => ARRAY(
					'port' => 25,
					'host' => 'example.smtp.server',
					'username' => 'username',
					'password' => 'password',
				),
				'message' => null,
				'header' => null,
				'created' => '2010-09-20 00:00:01',
				'modified' => '2010-09-20 00:00:01',
			)
		);
		$this->assertEqual($results, $expected);
	}

	function testDelete() {
		$this->Queues->params['url']['url'] = 'queues/delete';
		$this->Queues->delete(1);
		$count = $this->Queues->Queue->find('count');
		$this->assertEqual($count, 3);
	}

}
?>