<?php

App::import('Model', 'App');
App::import('Lib', 'Folder');
if (!class_exists('Cache')) {
	require LIBS . 'cache.php';
}

class CacheBehaviorTestCase extends CakeTestCase {

	var $fixtures = array('plugin.cacher.cache_data');

	function startTest() {
		$this->_cacheDisable = Configure::read('Cache.disable');
		Configure::write('Cache.disable', false);
		$this->CacheData =& ClassRegistry::init('CacheData');
		$this->CacheData->Behaviors->attach('Cacher.Cache');
	}

	function endTest() {
		Configure::write('Cache.disable', $this->_cacheDisable);
		unset($this->CacheData);
		ClassRegistry::flush();
	}

	function testRememberCache() {
		$settings = Cache::config('default');
		$oldPath = $settings['settings']['path'];

		$results = $this->CacheData->find('all', array(
			'conditions' => array(
				'CacheData.name LIKE' => '%cache%'
			)
		));

		$settings = Cache::config();
		$result = $settings['settings']['path'];
		$this->assertEqual($result, $oldPath);

		Cache::clear(false, 'cacheSourceCache');
	}

	function testSetup() {
		$this->CacheData->Behaviors->attach('Cacher.Cache', array('duration' => '+1 days'));
		$this->assertTrue(in_array('cache', ConnectionManager::sourceList()));

		// initilize the cache configuration and make sure it uses the settings we passed
		$ds = ConnectionManager::getDataSource('cache');
		$ds->setCachePath($this->CacheData);
		$settings = Cache::config('cacheSourceCache');
		$this->assertTrue($settings['settings']['duration'], '+1 days');
	}

	function testClearCache() {
		$results = $this->CacheData->find('all', array(
			'conditions' => array(
				'CacheData.name LIKE' => '%cache%'
			)
		));
		$results = Set::extract('/CacheData/name', $results);
		$expected = array(
			'A Cached Thing',
			'Cache behavior'
		);
		$this->assertEqual($results, $expected);

		$Folder = new Folder(CACHE.'cacher'.DS.'cache_data');
		$this->assertTrue($Folder->dirSize() > 0);

		// test clearing 1 cached query
		$ds = ConnectionManager::getDataSource('cache');
		$ds->setCachePath($this->CacheData);
		$this->CacheData->find('all', array('conditions' => array('CacheData.name LIKE' => '123')));
		$this->CacheData->find('all', array('conditions' => array('CacheData.name LIKE' => '456')));
		$results = $this->CacheData->clearCache(array('conditions' => array('CacheData.name LIKE' => '456')));
		$this->assertTrue($results);
		$this->assertTrue($Folder->dirSize() > 0);
		$key = $ds->_hash(array('conditions' => array('CacheData.name LIKE' => '456')));
		$this->assertFalse(Cache::read('cache_data_'.$key, $ds->cacheConfig));

		// test clearing all
		$this->assertTrue($this->CacheData->clearCache());
		$Folder = new Folder(CACHE.'cacher'.DS.'cache_data');
		$this->assertTrue($Folder->dirSize() === 0);
	}

	function testFind() {
		$dbConfigBefore = $this->CacheData->useDbConfig;
		$results = $this->CacheData->find('all', array(
			'conditions' => array(
				'CacheData.name LIKE' => '%cache%'
			)
		));
		$results = Set::extract('/CacheData/name', $results);
		$expected = array(
			'A Cached Thing',
			'Cache behavior'
		);
		$this->assertEqual($results, $expected);
		$this->assertEqual($dbConfigBefore, $this->CacheData->useDbConfig);

		// test that it's pulling from the cache
		$this->CacheData->delete(1);
		$results = $this->CacheData->find('all', array(
			'conditions' => array(
				'CacheData.name LIKE' => '%cache%'
			)
		));
		$results = Set::extract('/CacheData/name', $results);
		$expected = array(
			'A Cached Thing',
			'Cache behavior'
		);
		$this->assertEqual($results, $expected);

		Cache::clear(false, 'cacheSourceCache');
	}

}

?>
