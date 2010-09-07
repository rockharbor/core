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

	function testUseDifferentCacheEngine() {
		$this->CacheData->Behaviors->attach('Cacher.Cache', array('duration' => '+1 days', 'engine' => 'Xcache'));

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

		$ds = ConnectionManager::getDataSource('cache');
		Cache::clear(false, $ds->cacheConfig);
		Cache::clear(false, $ds->cacheMapConfig);
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

		$ds = ConnectionManager::getDataSource('cache');
		Cache::clear(false, $ds->cacheConfig);
		Cache::clear(false, $ds->cacheMapConfig);
	}

	function testSetup() {
		$this->CacheData->Behaviors->attach('Cacher.Cache', array('duration' => '+1 days'));
		$this->assertTrue(in_array('cache', ConnectionManager::sourceList()));

		$this->assertEqual($this->CacheData->useDbConfig, 'test_suite');
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

		// test that it wrote the cache

		// test clearing 1 cached query
		$ds = ConnectionManager::getDataSource('cache');
		$this->CacheData->find('all', array('conditions' => array('CacheData.name LIKE' => '123')));
		$this->CacheData->find('all', array('conditions' => array('CacheData.name LIKE' => '456')));
		$results = Cache::read('map', $ds->cacheMapConfig);
		$this->assertEqual(count($results['test_suite']['CacheData']), 3);
		$results = $this->CacheData->clearCache(array('conditions' => array('CacheData.name LIKE' => '456')));
		$this->assertTrue($results);
		$results = Cache::read('map', $ds->cacheMapConfig);
		$this->assertEqual(count($results['test_suite']['CacheData']), 2);

		// test clearing all
		$this->assertTrue($this->CacheData->clearCache());
		$results = Cache::read('map', $ds->cacheMapConfig);
		$this->assertEqual(count($results['test_suite']['CacheData']), 0);

		Cache::clear(false, $ds->cacheConfig);
		Cache::clear(false, $ds->cacheMapConfig);
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

		$ds = ConnectionManager::getDataSource('cache');
		Cache::clear(false, $ds->cacheConfig);
		Cache::clear(false, $ds->cacheMapConfig);
	}

}

?>
