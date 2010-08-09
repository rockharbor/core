<?php
/* Comments Test cases generated on: 2010-07-12 08:07:14 : 1278946994 */
App::import('Lib', 'CoreTestCase');
App::import('Component', 'QueueEmail');
App::import('Controller', 'Comments');

Mock::generate('QueueEmailComponent');
Mock::generatePartial('CommentsController', 'TestCommentsController', array('render', 'redirect', '_stop', 'header'));

class CommentsControllerTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('Comment', 'Group', 'CommentType');
		$this->Comments =& new TestCommentsController();
		$this->Comments->constructClasses();
		$this->Comments->QueueEmail = new MockQueueEmailComponent();
		$this->Comments->QueueEmail->setReturnValue('send', true);
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
		$results = Set::extract('/Comment', $vars['comments']);
		$expected = array(
			array(
				'Comment' => array(
					'id' => 1,
					'user_id' => 1,
					'comment_type_id' => 3,
					'comment' => 'another comment',
					'created_by' => NULL,
					'created' => '2010-03-24 09:53:55',
					'modified' => '2010-03-24 09:53:55'
				)
			),
			array(
				'Comment' => array(
					'id' => 2,
					'user_id' => 1,
					'comment_type_id' => 1,
					'comment' => 'comment\'d!',
					'created_by' => NULL,
					'created' => '2010-03-24 10:04:59',
					'modified' => '2010-03-24 10:04:59'
				)
			),
			array(
				'Comment' => array(
					'id' => 3,
					'user_id' => 1,
					'comment_type_id' => 1,
					'comment' => 'test',
					'created_by' => NULL,
					'created' => '2010-04-08 07:46:26',
					'modified' => '2010-04-08 07:46:26'
				)
			)
		);
		$this->assertEqual($results, $expected);

		$this->Comments->Session->write('User', array('Group' => array('id' => 5, 'lft' => 5)));
		$vars = $this->testAction('/comments/index/User:1', array(
			'return' => 'vars'
		));
		$results = Set::extract('/Comment', $vars['comments']);
		$expected = array(
			array(
				'Comment' => array(
					'id' => 2,
					'user_id' => 1,
					'comment_type_id' => 1,
					'comment' => 'comment\'d!',
					'created_by' => NULL,
					'created' => '2010-03-24 10:04:59',
					'modified' => '2010-03-24 10:04:59'
				)
			),
			array(
				'Comment' => array(
					'id' => 3,
					'user_id' => 1,
					'comment_type_id' => 1,
					'comment' => 'test',
					'created_by' => NULL,
					'created' => '2010-04-08 07:46:26',
					'modified' => '2010-04-08 07:46:26'
				)
			)
		);
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
				'comment_type_id' => 1,
				'comment' => 'This is a new comment'
			)
		);
		$vars = $this->testAction('/comments/add/User:1', array(
			'data' => $data,
			'return' => 'vars'
		));
		$this->Comments->Comment->id = 4;
		$results = $this->Comments->Comment->read();
		$this->assertEqual($results['Comment']['comment'], 'This is a new comment');
		$results = $vars['commentTypes'];
		$expected = array(
			1 => 'Staff',
			2 => 'Pastoral',
			3 => 'Admin'
		);
		$this->assertEqual($results, $expected);
	}

	function testEdit() {
		$data = array(
			'Comment' => array(
				'id' => 3,
				'comment' => 'This is an updated comment'
			)
		);
		$vars = $this->testAction('/comments/edit/3/User:3', array(
			'data' => $data,
			'return' => 'vars'
		));
		$this->Comments->Comment->id = 3;
		$results = $this->Comments->Comment->read();
		$this->assertEqual($results['Comment']['comment'], 'This is an updated comment');
		$results = $vars['commentTypes'];
		$expected = array(
			1 => 'Staff',
			2 => 'Pastoral',
			3 => 'Admin'
		);
		$this->assertEqual($results, $expected);

		$this->Comments->Session->write('User', array('Group' => array('id' => 5, 'lft' => 5)));
		$vars = $this->testAction('/comments/edit/3/User:3', array(
			'data' => $data,
			'return' => 'vars'
		));
		$results = $vars['commentTypes'];
		$expected = array(
			1 => 'Staff',
		);
		$this->assertEqual($results, $expected);
	}

	function testDelete() {
		$this->testAction('/comments/delete/1');
		$result = $this->Comments->Comment->read(null, 1);
		$this->assertFalse($result);
	}

}
?>