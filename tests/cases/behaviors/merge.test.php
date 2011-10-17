<?php
/* GeoCoordinate Test cases generated on: 2010-07-07 07:07:30 : 1278513210 */
App::import('Lib', 'CoreTestCase');
App::import('Behavior', 'Merge');
App::import('Model', 'User');

class MergeBehaviorTestCase extends CoreTestCase {
	
	function startTest() {
		$this->Merge =& new MergeBehavior();
	}

	function endTest() {
		unset($this->Merge);
		ClassRegistry::flush();
	}
	
	function testUnbindRebindAll() {
		$user = new User(array('ds' => 'test_suite'));
		$hasMany = $user->hasMany;
		
		$this->Merge->unbindAll($user);
		$this->assertTrue(empty($user->belongsTo));
		$this->assertTrue(empty($user->hasOne));
		$this->assertTrue(empty($user->hasMany));
		$this->assertTrue(empty($user->hasAndBelongsToMany));
		
		$this->assertEqual($hasMany, $this->Merge->_assocs['User']['hasMany']);
		
		$this->Merge->rebindAll($user);
		$this->assertTrue(!empty($user->belongsTo));
		$this->assertTrue(!empty($user->hasOne));
		$this->assertTrue(!empty($user->hasMany));
		$this->assertTrue(!empty($user->hasAndBelongsToMany));
	}
	
}