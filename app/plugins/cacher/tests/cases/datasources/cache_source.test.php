<?php

App::import('Model', 'App');
if (!class_exists('Cache')) {
	require LIBS . 'cache.php';
}

class CacheSourceTestCase extends CakeTestCase {

	var $fixtures = array('plugin.cacher.cache_data');

	function startTest() {
		$this->_cacheDisable = Configure::read('Cache.disable');		
		Configure::write('Cache.disable', false);
		$this->CacheData =& ClassRegistry::init('CacheData');
		if (!in_array('cache', ConnectionManager::sourceList())) {
			 ConnectionManager::create('cache', array(
				'original' => $this->CacheData->useDbConfig,
				'datasource' => 'Cacher.cache'
			));
		}
		$this->dataSource =& ConnectionManager::getDataSource('cache');
	}

	function endTest() {
		Configure::write('Cache.disable', $this->_cacheDisable);
		unset($this->CacheData);
		unset($this->dataSource);
		ClassRegistry::flush();
	}

	function testUseExistingConfig() {
		Cache::config('cacheTest', array(
			'engine' => 'File',
			'duration' => '+1 days',
			'path' => CACHE.'cacher_test'
		));

		ConnectionManager::create('newCache', array(
			'config' => 'cacheTest',
			'original' => $this->CacheData->useDbConfig,
			'datasource' => 'Cacher.cache'
		));
		$this->dataSource =& ConnectionManager::getDataSource('newCache');

		$key = $this->dataSource->_hash(array('conditions' => array()));
		$results = $this->dataSource->read($this->CacheData, array('conditions' => array()));

		$result = Cache::config($this->dataSource->cacheConfig);
		$expected = CACHE.'cacher_test'.DS.'cache_data';
		$this->assertEqual($result['settings']['path'], $expected);

		$this->assertTrue(Cache::read('cache_data_'.$key, $this->dataSource->cacheConfig));

		Cache::clear(false, $this->dataSource->cacheConfig);
	}

	function testRead() {
		$conditions = array(
			'conditions' => array(
				'CacheData.id' => 1
			)
		);
		$key = $this->dataSource->_hash($conditions);
		
		$results = $this->dataSource->read($this->CacheData, $conditions);
		// test that we get the correct data
		$this->assertEqual(Set::extract('/CacheData/name', $results), array('A Cached Thing'));
		// test that we wrote to the cache
		$this->assertTrue(Cache::read('cache_data_'.$key, $this->dataSource->cacheConfig));
		// test that the cache results match the results
		$this->assertEqual(Cache::read('cache_data_'.$key, $this->dataSource->cacheConfig), $results);
		
		// test multiple cached results
		$moreConditions = array(
			'conditions' => array(
				'CacheData.name' => 'non-existent'
			)
		);
		$results = $this->dataSource->read($this->CacheData, $moreConditions);
		$this->assertEqual($results, array());

		// delete from the db and make sure we read from the cache
		$this->CacheData->delete(1);
		$results = $this->dataSource->read($this->CacheData, $conditions);
		$this->assertEqual(Set::extract('/CacheData/name', $results), array('A Cached Thing'));

		Cache::clear(false, $this->dataSource->cacheConfig);
	}

	function testSetCachePath() {
		$empty = new Model(array(
			'name' => 'EmptyThing',
			'table' => false
		));
		$this->dataSource->setCachePath($empty);
		$results = Cache::config($this->dataSource->cacheConfig);
		$expected = CACHE.'cacher'.DS.'empty_thing';
		$this->assertEqual($results['settings']['path'], $expected);
	}

	function testHash() {
		$query = array(
			'conditions' => array(
				'SomeModel.name' => 'CakePHP'
			)
		);
		$this->assertTrue(is_string($this->dataSource->_hash($query)));
	}
}

?>