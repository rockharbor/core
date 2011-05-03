<?php
/* Roster Test cases generated on: 2010-07-26 14:07:11 : 1280180951 */
App::import('Lib', 'CoreTestCase');
App::import('Model', 'Profile');

class ProfileTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('Profile');
		$this->loadFixtures('Involvement', 'Leader', 'Ministry', 'Campus');
		$this->Profile =& ClassRegistry::init('Profile');
	}

	function endTest() {
		unset($this->Profile);
		ClassRegistry::flush();
	}

	function testVirtualFields() {
		$profile = $this->Profile->read(null, 1);
		$profile['Profile']['birth_date'] = date('Y-m-d', strtotime('-20 years'));
		$this->Profile->save($profile);
		$profile = $this->Profile->read(null, 1);

		$result = $profile['Profile']['age'];
		$this->assertWithinMargin($result, 20, 1);

		$result = $profile['Profile']['name'];
		$this->assertEqual($result, 'Jeremy Harris');

		$result = $profile['Profile']['child'];
		$this->assertFalse($result, 0);

		$profile = $this->Profile->read(null, 1);
		$profile['Profile']['birth_date'] = date('Y-m-d', strtotime('-12 years'));
		$this->Profile->save($profile);
		$profile = $this->Profile->read(null, 1);

		$result = $profile['Profile']['age'];
		$this->assertWithinMargin($result, 12, 1);

		$result = $profile['Profile']['child'];
		$this->assertFalse($result, 0);

		$profile = $this->Profile->read(null, 1);
		$profile['Profile']['birth_date'] = date('Y-m-d', strtotime('-12 years'));
		$profile['Profile']['adult'] = false;
		$this->Profile->save($profile);
		$profile = $this->Profile->read(null, 1);

		$result = $profile['Profile']['age'];
		$this->assertWithinMargin($result, 12, 1);

		$result = $profile['Profile']['child'];
		$this->assertTrue($result, 1);

		$profile = $this->Profile->read(null, 1);
		$profile['Profile']['birth_date'] = null;
		$profile['Profile']['adult'] = false;
		$this->Profile->save($profile);
		$profile = $this->Profile->read(null, 1);

		$result = $profile['Profile']['child'];
		$this->assertTrue($result, 1);

		$profile = $this->Profile->read(null, 1);
		$this->assertTrue($profile['Profile']['leading']);

		$profile = $this->Profile->read(null, 3);
		$this->assertFalse($profile['Profile']['leading']);

		$profile = $this->Profile->read(null, 2);
		$this->assertFalse($profile['Profile']['managing']);

		$profile = $this->Profile->read(null, 2);
		$this->assertFalse($profile['Profile']['leading']);
	}

}
?>