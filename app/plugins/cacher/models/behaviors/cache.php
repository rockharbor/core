<?php
/**
 * Cache behavior class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       cacher
 * @subpackage    cacher.models.behaviors
 */

/**
 * Cache Behavior
 *
 * Auto-caches find results into the cache. Running an exact find again will
 * pull from the cache. Requires the CacheSource datasource.
 *
 * @package       cacher
 * @subpackage    cacher.models.behaviors
 */
class CacheBehavior extends ModelBehavior {

/**
 * Settings
 *
 * @param array
 */
	var $settings;

/**
 * The model's original DbConfig
 *
 * @var string
 */
	var $_originalDbConfig = null;

/**
 * The original cache configuration
 *
 * @var string
 */
	var $_originalCacheConfig = null;

/**
 * Sets up a connection using passed settings
 *
 * ### Config
 * - `config` The name of an existing Cache configuration to duplicate
 * - any options taken by Cache::config() will be used if `config` is not defined
 *
 * @param Model $Model The calling model
 * @param array $config Configuration settings
 * @see Cache::config()
 */
	function setup(&$Model, $config = array()) {
		$_defaults = array(
			'config' => null,
			'engine' => 'File',
			'duration' => '+6 hours'
		);
		$settings = array_merge($_defaults, $config);

		$this->_originalDbConfig = $Model->useDbConfig;

		if (!in_array('cache', ConnectionManager::sourceList())) {
			$settings['original'] = $Model->useDbConfig;
			$settings['datasource'] = 'Cacher.cache';
			ConnectionManager::create('cache', $settings);
		}

		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = $settings;
		}
		$this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], $settings);
	}

/**
 * Intercepts find to use the caching datasource instead
 *
 * @param Model $Model The calling model
 * @param array $queryData The query data sent
 * @return Modified query
 */
	function beforeFind(&$Model, $queryData) {
		$cache = Cache::getInstance();
		$this->_originalCacheConfig = $cache->__name;
		$Model->setDataSource('cache');
		return $queryData;
	}

/**
 * Resets the datasource
 *
 * @param Model $Model The calling model
 */
	function afterFind(&$Model) {
		Cache::config($this->_originalCacheConfig);
		$Model->setDataSource($this->_originalDbConfig);
	}

/**
 * Clears all of the cache for this model's find queries. Optionally, pass
 * `$queryData` to just clear a specific query
 *
 * @param Model $Model The calling model
 * @return boolean
 */
	function clearCache(&$Model, $queryData = null) {
		if ($queryData !== null) {
			$queryData = $this->_prepareFind($Model, $queryData);
		}
		$cache = Cache::getInstance();
		$this->_originalCacheConfig = $cache->__name;
		$ds = ConnectionManager::getDataSource('cache');
		$success = $ds->clearModelCache($Model, $queryData);
		Cache::config($this->_originalCacheConfig);
		return $success;
	}

/*
 * Prepares a query by adding missing data. This function is needed because
 * reads on the database typically bypass Model::find() which is where the query
 * is changed.
 *
 * @param array $query The query
 * @return array The modified query
 * @access protected
 * @see Model::find()
 */
	function _prepareFind($Model, $query = array()) {
		$query = array_merge(
			array(
				'conditions' => null, 'fields' => null, 'joins' => array(), 'limit' => null,
				'offset' => null, 'order' => null, 'page' => null, 'group' => null, 'callbacks' => true
			),
			(array)$query
		);
		if (!is_numeric($query['page']) || intval($query['page']) < 1) {
			$query['page'] = 1;
		}
		if ($query['page'] > 1 && !empty($query['limit'])) {
			$query['offset'] = ($query['page'] - 1) * $query['limit'];
		}
		if ($query['order'] === null && $Model->order !== null) {
			$query['order'] = $Model->order;
		}
		$query['order'] = array($query['order']);

		return $query;
	}
}

?>