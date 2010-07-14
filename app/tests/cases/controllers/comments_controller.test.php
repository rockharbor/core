<?php
/* Comments Test cases generated on: 2010-07-12 08:07:14 : 1278946994 */
App::import('Controller', 'Comments');

class TestCommentsController extends CommentsController {
	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}

	function _stop($status = 0) {
		$this->stopped = $status;
	}
}

class CommentsControllerTestCase extends CakeTestCase {
	var $fixtures = array('app.ministries_rev', 'app.involvements_rev','app.notification', 'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category', 'app.school', 'app.campus', 'plugin.media.attachment', 'app.ministry', 'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date', 'app.payment_option', 'app.question', 'app.roster', 'app.role', 'app.roster_status', 'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type', 'app.comments', 'app.notification', 'app.image', 'plugin.media.document', 'app.household_member', 'app.household', 'app.publication', 'app.publications_user', 'app.log', 'app.app_setting', 'app.alert', 'app.alerts_user', 'app.log');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		$this->loadFixtures('Comment', 'Group', 'CommentType');
		$this->Comments =& new TestCommentsController();
		$this->Comments->constructClasses();
		$this->Comments->Component->initialize($this->Comments);
		$this->Comments->Session->write('Auth.User', array('id' => 1));
		$this->Comments->Session->write('User', array('Group' => array('id' => 1, 'lft' => 1)));
	}

	function endTest() {
		$this->Comments->Session->destroy();
		unset($this->Comments);		
		ClassRegistry::flush();
	}

	function testIndex() {
		$vars = $this->testAction('/test_comments/index/User:1', array(
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
		$vars = $this->testAction('/test_comments/index/User:1', array(
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

		$vars = $this->testAction('/test_comments/index/User:2', array(
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
		$vars = $this->testAction('/test_comments/add', array(
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
		$vars = $this->testAction('/test_comments/edit/3', array(
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
		$vars = $this->testAction('/test_comments/edit/3', array(
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
		$this->testAction('/test_comments/delete/1');
		$result = $this->Comments->Comment->read(null, 1);
		$this->assertFalse($result);
	}

}
?>