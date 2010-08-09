<?php
/* SysEmail Test cases generated on: 2010-08-05 09:08:32 : 1281025892 */
App::import('Model', array('SysEmail', 'Document'));

class SysEmailTestCase extends CakeTestCase {
	var $fixtures = array(
		'app.user', 'app.group', 'app.profile', 'app.classification', 'app.job_category',
		'app.school', 'app.campus', 'app.attachment', 'app.ministry', 'app.involvement',
		'app.involvement_type', 'app.address', 'app.zipcode', 'app.region', 'app.date',
		'app.payment_option', 'app.question', 'app.roster', 'app.role',
		'app.answer', 'app.payment', 'app.payment_type', 'app.leader', 'app.comment', 'app.comment_type',
		'app.comments', 'app.notification', 'app.household_member',
		'app.household', 'app.publication', 'app.publications_user', 'app.log', 'app.ministries_rev', 'app.involvements_rev'
	);

	var $autoFixtures = false;

	function startTest() {
		$this->loadFixtures('Attachment');
		$this->SysEmail =& ClassRegistry::init('SysEmail');
		$this->Document =& ClassRegistry::init('Document');
	}

	function endTest() {
		unset($this->SysEmail);
		unset($this->Document);
		ClassRegistry::flush();
	}

	function testGcAttachment() {
		$this->SysEmail->gcAttachments('anothertest');
		$results = $this->Document->find('all', array(
			'fields' => array('id'),
			'conditions' => array(
				'model' => 'SysEmail'
			)
		));
		$this->assertEqual(count($results), 2);

		$attachment = $this->Document->read(null, 1);
		$attachment['Document']['created'] = date('Y-m-d');
		$this->Document->save($attachment);
		$this->SysEmail->gcAttachments();
		$results = $this->Document->find('all', array(
			'fields' => array('id'),
			'conditions' => array(
				'model' => 'SysEmail'
			)
		));
		$this->assertEqual(count($results), 1);
	}

	function testGcAttachmentsAll() {
		$this->SysEmail->gcAttachments();
		$results = $this->Document->find('all', array(
			'fields' => array('id'),
			'conditions' => array(
				'model' => 'SysEmail'
			)
		));
		$this->assertEqual(count($results), 0);
	}

}
?>