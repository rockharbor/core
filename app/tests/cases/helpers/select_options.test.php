<?php
App::import('Helper', array('SelectOptions'));

class SelectOptionsHelperTestCase extends CakeTestCase {

	function startTest() {
		$this->SelectOptions = new SelectOptionsHelper();
	}

	function endTest() {
		unset($this->SelectOptions);
		ClassRegistry::flush();
	}

	function testValue() {
		$View = new View(new Controller());
		$View->set('jobCategories', array(1 => 'Web Development'));

		$profile = array(
			'Profile' => array(
				'job_category_id' => 1
			)
		);
		$result = $this->SelectOptions->value('Profile.job_category_id', $profile);
		$this->assertEqual($result, 'Web Development');

		$profile['Profile']['job_category_id'] = 2;
		$result = $this->SelectOptions->value('Profile.job_category_id', $profile);
		$this->assertEqual($result, '&nbsp;');

		$result = $this->SelectOptions->value('Profile.job_category_id', $profile, null);
		$this->assertNull($result);

		unset($View->viewVars['jobCategories']);
		$result = $this->SelectOptions->value('Profile.job_category_id', $profile);
		$this->assertEqual($result, 2);

		$results = $this->SelectOptions->value('Profile.anything_without_data');
		$this->assertEqual($results, '&nbsp;');
	}

	function testMagicCall() {
		$this->assertFalse(method_exists($this->SelectOptions, 'gender'));
		$this->assertEqual($this->SelectOptions->gender('m'), 'Male');

		$this->assertFalse(method_exists($this->SelectOptions, 'maritalStatus'));
		$this->assertEqual($this->SelectOptions->maritalStatus('d'), 'Divorced');

		$this->assertFalse(method_exists($this->SelectOptions, 'grade'));
		$this->assertEqual($this->SelectOptions->grade(-1), 'Pre-kinder');

		$this->expectError();
		$this->assertFalse($this->SelectOptions->nonExistentMap);
	}

}
?>