<?php
/* Comments Test cases generated on: 2010-07-12 08:07:14 : 1278946994 */
App::import('Lib', 'CoreTestCase');
App::import('Component', array('QueueEmail.QueueEmail', 'Notifier'));
App::import('Controller', 'Comments');

Mock::generatePartial('QueueEmailComponent', 'MockCommentsQueueEmailComponent', array('_smtp', '_mail'));
Mock::generatePartial('NotifierComponent', 'MockCommentsNotifierComponent', array('_render'));
Mock::generatePartial('CommentsController', 'TestCommentsController', array('render', 'redirect', '_stop', 'header'));

class CommentsControllerTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('Comment', 'Group');
		$this->Comments =& new TestCommentsController();
		$this->Comments->__construct();
		$this->Comments->constructClasses();
		$this->Comments->Notifier = new MockCommentsNotifierComponent();
		$this->Comments->Notifier->initialize($this->Comments);
		$this->Comments->Notifier->setReturnValue('_render', 'Notification body text');
		$this->Comments->Notifier->QueueEmail = new MockCommentsQueueEmailComponent();
		$this->Comments->Notifier->QueueEmail->setReturnValue('_smtp', true);
		$this->Comments->Notifier->QueueEmail->setReturnValue('_mail', true);
		$this->testController = $this->Comments;
	}

	function endTest() {
		$this->Comments->Session->destroy();
		unset($this->Comments);		
		ClassRegistry::flush();
	}

	function testIndex() {
		$vars = $this->testAction('/comments/index/User:1', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Comment/id', $vars['comments']);
		$expected = array(2, 4);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/comments/index/User:1', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Comment/id', $vars['comments']);
		$expected = array(2, 4);
		$this->assertEqual($results, $expected);

		$vars = $this->testAction('/comments/index/User:2', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Comment', $vars['comments']);
		$this->assertEqual($results, array());
	}

	function testAdd() {
		$data = array(
			'Comment' => array(
				'user_id' => 1,
				'created_by' => 1,
				'group_id' => 1,
				'comment' => 'This is a new comment'
			)
		);
		$vars = $this->testAction('/comments/add/User:1', array(
			'data' => $data,
			'return' => 'vars'
		));
		$this->Comments->Comment->id = 5;
		$results = $this->Comments->Comment->read();
		$this->assertEqual($results['Comment']['comment'], 'This is a new comment');
	}

	function testEdit() {
		$data = array(
			'Comment' => array(
				'id' => 3,
				'comment' => 'This is an updated comment'
			)
		);
		$vars = $this->testAction('/comments/edit/Comment:3/User:3', array(
			'data' => $data,
			'return' => 'vars'
		));
		$this->Comments->Comment->id = 3;
		$results = $this->Comments->Comment->read();
		$this->assertEqual($results['Comment']['comment'], 'This is an updated comment');
		$results = $vars['groups'];
		$expected = array(
			1 => 'Super Administrator',
			2 => 'Administrator',
			3 => 'Pastor',
			4 => 'Communications Admin',
			5 => 'Staff',
			6 => 'Intern',
			7 => 'Developer',
			8 => 'User'
		);
		$this->assertEqual($results, $expected);
	}

	function testDelete() {
		$this->testAction('/comments/delete/Comment:1/User:1');
		$result = $this->Comments->Comment->read(null, 1);
		$this->assertFalse($result);
	}

}
?>