<?php
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Comment');

class CommentTestCase extends CoreTestCase {

	public function startTest($method) {
		parent::startTest($method);
		$this->loadFixtures('Comment', 'Group', 'User');
		$this->Comment =& ClassRegistry::init('Comment');
	}

	public function endTest() {
		unset($this->Comment);
		ClassRegistry::flush();
	}

	public function testCanDelete() {
		$this->assertFalse($this->Comment->canDelete());
		$this->assertFalse($this->Comment->canDelete(3));
		$this->assertFalse($this->Comment->canDelete(3, 1));
		$this->assertTrue($this->Comment->canDelete(3, 4));
		$this->assertTrue($this->Comment->canDelete(1, 1));
		$this->assertFalse($this->Comment->canDelete(1, 4));
		$this->assertFalse($this->Comment->canDelete(1, 2));
	}

	public function testCanEdit() {
		$this->assertTrue($this->Comment->canEdit(1, 1));
		$this->assertFalse($this->Comment->canEdit());
	}

}
