<?php
App::import('Lib', 'CoreTestCase');
App::import('Controller', 'App');

class AppControllerTestCase extends CoreTestCase {
	var $fixtures = array('app.notification', 'app.user', 'app.group',
		'app.profile', 'app.classification', 'app.job_category', 'app.school',
		'app.campus', 'plugin.media.attachment', 'app.ministry',
		'app.involvement', 'app.involvement_type', 'app.address', 'app.zipcode',
		'app.region', 'app.date', 'app.payment_option', 'app.question',
		'app.roster', 'app.role', 'app.answer',
		'app.payment', 'app.payment_type', 'app.leader', 'app.comment',
		'app.comment_type', 'app.comments', 'app.notification', 'app.image',
		'plugin.media.document', 'app.household_member', 'app.household',
		'app.publication', 'app.publications_user', 'app.log', 'app.app_setting',
		'app.alert', 'app.alerts_user', 'app.aro', 'app.aco', 'app.aros_aco',
		'app.ministries_rev', 'app.involvements_rev');

/**
 * Disable inserting all records by default. Use CakeTestCase::loadFixtures
 * to load the data needed for the test (or case).
 */
	var $autoFixtures = false;

	function startTest() {
		$this->loadFixtures('User', 'Group', 'Notification', 'Alert', 'Aco');
		$this->loadFixtures('Aro', 'ArosAco', 'Household', 'HouseholdMember');
		$this->loadFixtures('Leader', 'Campus', 'Ministry', 'Involvement');
		$this->App =& new AppController();
		$this->App->constructClasses();
		$this->App->Component->initialize($this->App);
		$this->App->activeUser = array(
			'User' => array('id' => 1),
			'Group' => array('id' => 1)
		);
	}

	function endTest() {
		$this->App->Session->destroy();
		unset($this->App);
		ClassRegistry::flush();
	}

	function test_setConditionalGroups() {
		$this->App->passedArgs = array('User' => 1);
		$this->App->_setConditionalGroups();
		$results = $this->App->activeUser['ConditionalGroup'];
		$expected = array(
			'id' => 12,
			'name' => 'Owner',
			'conditional' => 1,
			'created' => '2010-07-07 11:15:22',
			'modified' => '2010-07-07 11:15:22',
			'parent_id' => 8,
			'lft' => 15,
			'rght' => 18
		);
		$this->assertEqual($results, $expected);

		$this->App->activeUser['User']['id'] = 2;
		$this->App->passedArgs = array('User' => 3);
		$this->App->_setConditionalGroups();
		$results = $this->App->activeUser['ConditionalGroup'];
		$expected = array(
			'id' => 13,
			'name' => 'Household Contact',
			'conditional' => 1,
			'created' => '2010-07-07 11:15:22',
			'modified' => '2010-07-07 11:15:22',
			'parent_id' => 12,
			'lft' => 16,
			'rght' => 17
		);
		$this->assertEqual($results, $expected);

		$this->App->activeUser['User']['id'] = 3;
		$this->App->passedArgs = array('Campus' => 1);
		$this->App->_setConditionalGroups();
		$results = isset($this->App->activeUser['ConditionalGroup']);
		$this->assertFalse($results);

		$this->App->activeUser['User']['id'] = 1;
		$this->App->passedArgs = array('Campus' => 1);
		$this->App->_setConditionalGroups();
		$results = $this->App->activeUser['ConditionalGroup'];
		$expected = array(
			'id' => 9,
			'name' => 'Campus Manager',
			'conditional' => 1,
			'created' => '2010-07-07 11:15:22',
			'modified' => '2010-07-07 11:15:22',
			'parent_id' => 8,
			'lft' => 9,
			'rght' => 14
		);
		$this->assertEqual($results, $expected);

		$this->App->activeUser['User']['id'] = 1;
		$this->App->passedArgs = array('Ministry' => 4);
		$this->App->_setConditionalGroups();
		$results = $this->App->activeUser['ConditionalGroup'];
		$expected = array(
			'id' => 10,
			'name' => 'Ministry Manager',
			'conditional' => 1,
			'created' => '2010-07-07 11:15:22',
			'modified' => '2010-07-07 11:15:22',
			'parent_id' => 9,
			'lft' => 10,
			'rght' => 13
		);
		$this->assertEqual($results, $expected);

		$this->App->activeUser['User']['id'] = 1;
		$this->App->passedArgs = array('Involvement' => 1);
		$this->App->_setConditionalGroups();
		$results = $this->App->activeUser['ConditionalGroup'];
		$expected = array(
			'id' => 11,
			'name' => 'Involvement Leader',
			'conditional' => 1,
			'created' => '2010-07-07 11:15:22',
			'modified' => '2010-07-07 11:15:22',
			'parent_id' => 10,
			'lft' => 11,
			'rght' => 12
		);
		$this->assertEqual($results, $expected);
	}

	function testIsAuthorized() {
		$this->App->activeUser = array(
			'User' => array('id' => 1),
			'Group' => array('id' => 8)
		);

		$result = $this->App->isAuthorized('involvements/revise');
		$this->assertFalse($result);

		$result = $this->App->isAuthorized('involvements/view');
		$this->assertTrue($result);
	}

	function test_editSelf() {
		$this->App->action = 'edit';
		$this->App->_editSelf('edit');
		$this->App->_setConditionalGroups();
		$results = $this->App->activeUser['ConditionalGroup'];
		$expected = array(
			'id' => 12,
			'name' => 'Owner',
			'conditional' => 1,
			'created' => '2010-07-07 11:15:22',
			'modified' => '2010-07-07 11:15:22',
			'parent_id' => 8,
			'lft' => 15,
			'rght' => 18
		);
		$this->assertEqual($results, $expected);
	}
}

?>
