<?php
/**
 * Cache data source class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       cacher
 * @subpackage    cacher.models.datasources
 */

/**
 * Includes
 */
App::import('Lib', 'Folder');

/**
 * CacheSource datasource
 *
 * Gets find results from cache instead of the original datasource. The cache
 * is stored under CACHE/find_results/{model alias}. Each model has separate
 * cache so you can easily clear it on a per-model basis.
 *
 * @package       cacher
 * @subpackage    cacher.models.datasources
 */
class CacheSource extends DataSource {

/**
 * Stored original datasource for fallback methods
 *
 * @var DataSource
 */
	var $source = null;

/**
 * The name of the cache configuration for this datasource instance
 *
 * @var string
 */
	var $cacheConfig = 'CacherResults';


/**
 * The name of the cache's map configuration for this datasource instance
 *
 * @var string
 */
	var $cacheMapConfig = 'CacherMap';

	var $_clearCache = null;

/**
 * Constructor
 *
 * Sets default options if none are passed when the datasource is created and
 * creates the cache configuration. If a `config` is passed and is a valid
 * Cache configuration, CacheSource uses its settings
 *
 * ### Extra config settings
 * - `original` The name of the original datasource, i.e., 'default' (required)
 * - `config` The name of the Cache configuration to duplicate (optional)
 * - other settings required by DataSource...
 *
 * @param array $config Configure options
 */
	function __construct($config = array()) {
		$config = array_merge(array(
			'clearOnSave' => false,
			'clearOnDelete' => false
		), (array)$config);
		parent::__construct($config);
		if (!isset($this->config['original'])) {
			trigger_error('Cacher.CacheSource::__construct() :: Missing name of original datasource', E_USER_WARNING);
		}
		$settings = array(
			'engine' => 'File',
			'duration' => '+6 hours',
			'path' => CACHE.'cacher',
			'prefix' => 'cacher_'
		);
		if (isset($this->config['config']) && Cache::isInitialized($this->config['config'])) {
			$_existingCache = Cache::config($this->config['config']);
			$settings = array_merge($settings, $_existingCache['settings']);
		}
		$this->_clearCache = array(
			'save' => $this->config['clearOnSave'],
			'delete' => $this->config['clearOnDelete']
		);

		$this->source =& ConnectionManager::getDataSource($this->config['original']);

		new Folder(CACHE.'cacher', true, 0775);
		Cache::config($this->cacheConfig, $settings);
		Cache::config($this->cacheMapConfig, array(
			'engine' => 'File',
			'duration' => '+10 years',
			'path' => CACHE.'cacher',
			'prefix' => 'cacher_'
		));
		$map = Cache::read('map', $this->cacheMapConfig);
		if ($map === false) {
			Cache::write('map', array(), $this->cacheMapConfig);
		}
	}

/*
 * Fallback to original datasource's functions
 *
 * @param string $name The name of the method
 * @param array $arguments The arguments
 */
	function __call($name, $arguments) {
		$Args = array();
		foreach($arguments as $k => &$arg) {
			$Args[$k] = &$arg;
		}
		call_user_func_array(array(&$this->source, $name), $Args);
	}

/**
 * Fallback to original datasource's function
 *
 * @param Model $Model
 * @return array
 * @see DataSource::describe()
 */
	function describe($Model) {
		return $this->source->describe($Model);
	}

/**
 * Fallback to original datasource's function
 *
 * @param array $data
 * @return array
 * @see DataSource::listSources()
 */
	function listSources($data) {
		return $this->source->listSources($data);
	}

/**
 * Fallback to original datasource's function
 *
 * @param Model $Model
 * @param array $fields
 * @param array $values
 * @see DataSource::create()
 */
	function create($Model, $fields = null, $values = null) {
		if ($this->_clearCache['save']) {
			$this->clearModelCache($Model);
		}
		return $this->source->create($Model, $fields, $values);
	}

/**
 * Reads from cache if it exists. If not, it falls back to the original
 * datasource to retrieve the data and cache it for later
 *
 * @param Model $Model
 * @param array $queryData
 * @return array Results
 * @see DataSource::read()
 */
	function read($Model, $queryData = array()) {
		if (Configure::read('Cache.disable')) {
			return $this->source->read($Model, $queryData);
		}
		$key = $this->_key($Model, $queryData);
		$results = Cache::read($key, $this->cacheConfig);
		if ($results == false) {		
			$results = $this->source->read($Model, $queryData);
			Cache::write($key, $results, $this->cacheConfig);
			$map = Cache::read('map', $this->cacheMapConfig);
			$sourceName = ConnectionManager::getSourceName($this->source);
			if (!isset($map[$sourceName])) {
				$map[$sourceName] = array();
			}
			if (!isset($map[$sourceName][$Model->alias])) {
				$map[$sourceName][$Model->alias] = array();
			}
			$map = Set::merge($map, array(
				$sourceName => array(
					$Model->alias => array(
						$key
					)
				)
			));
			Cache::write('map', $map, $this->cacheMapConfig);
		}
		return $results;
	}

/**
 * Fallback to original datasource's function
 *
 * @param Model $Model
 * @param array $fields
 * @param array $values
 * @see DataSource::update()
 */
	function update($Model, $fields = null, $values = null) {
		if ($this->_clearCache['save']) {
			$this->clearModelCache($Model);
		}
		return $this->source->update($Model, $fields, $values);
	}

/**
 * Fallback to original datasource's function
 *
 * @param Model $Model
 * @param integer $id
 * @see DataSource::update()
 */
	function delete($Model, $id = null) {
		if ($this->_clearCache['delete']) {
			$this->clearModelCache($Model);
		}
		return $this->source->delete($Model, $id);
	}

/*
 * Clears the cache for a specific model and rewrites the map. Pass query to
 * clear a specific query's cached results
 *
 * @param array $query If null, clears all for this model
 * @param Model $Model The model to clear the cache for
 */
	function clearModelCache($Model, $query = null) {
		$map = Cache::read('map', $this->cacheMapConfig);
		$sourceName = ConnectionManager::getSourceName($this->source);
		if (isset($map[$sourceName]) && isset($map[$sourceName][$Model->alias])) {
			foreach ($map[$sourceName][$Model->alias] as $key => $modelCacheKey) {
				if ($query !== null) {
					$findKey = $this->_key($Model, $query);
					if ($modelCacheKey == $findKey) {
						Cache::delete($modelCacheKey, $this->cacheConfig);
						unset($map[$sourceName][$Model->alias][$key]);
					}
				} else {
					Cache::delete($modelCacheKey, $this->cacheConfig);
					unset($map[$sourceName][$Model->alias][$key]);
				}
			}
			Cache::write('map', $map, $this->cacheMapConfig);
		}
		return true;
	}

/**
 * Hashes a query into a unique string and creates a cache key
 *
 * @param Model $Model The model
 * @param array $query The query
 * @return string
 * @access protected
 */
	function _key($Model, $query) {
		$query = array_merge(
			array(
				'conditions' => null, 'fields' => null, 'joins' => array(), 'limit' => null,
				'offset' => null, 'order' => null, 'page' => null, 'group' => null, 'callbacks' => true
			),
			(array)$query
		);
		$queryHash = md5(serialize($query));
		$sourceName = ConnectionManager::getSourceName($this->source);
		return Inflector::underscore($sourceName).'_'.Inflector::underscore($Model->alias).'_'.$queryHash;
	}

}

?>