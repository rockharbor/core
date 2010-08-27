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
 * cache so you can easily clear it on a per-model basis. In order to clear it
 * for the specified model, you must use CacheSource::setCachePath first.
 *
 * @package       cacher
 * @subpackage    cacher.models.datasources
 * @todo cache based on original datasource so not to have conflicting models
 */
class CacheSource extends DataSource {

/**
 * Stored original datasource for fallback methods
 *
 * @var DataSource
 */
	var $source = null;

/**
 * The root cache path
 *
 * @var string
 * @access protected
 */
	var $_rootPath = null;

/**
 * Stored cache config settings
 *
 * @var array
 */
	var $_settings = array();

/**
 * The name of the cache configuration for this datasource instance
 *
 * @var string
 */
	var $cacheConfig = null;

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
		parent::__construct($config);
		if (!isset($this->config['original'])) {
			trigger_error('Cacher.CacheSource::__construct() :: Missing name of original datasource', E_USER_WARNING);
		}

		$settings = array(
			'engine' => 'File',
			'duration' => '+6 hours',
			'path' => CACHE.'cacher'
		);
		if (isset($this->config['config']) && Cache::isInitialized($this->config['config'])) {
			$_existingCache = Cache::config($this->config['config']);
			$settings = array_merge($settings, $_existingCache['settings']);
		}
		$this->_settings = $settings;

		$this->source =& ConnectionManager::getDataSource($this->config['original']);
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
		$this->setCachePath($Model);
		$key = $this->_hash($queryData);
		$results = Cache::read(Inflector::underscore($Model->alias).'_'.$key, $this->cacheConfig);
		if ($results == false) {		
			$results = $this->source->read($Model, $queryData);
			Cache::write(Inflector::underscore($Model->alias).'_'.$key, $results, $this->cacheConfig);
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
		return $this->source->delete($Model, $id);
	}

/**
 * Sets the cache path based on the model
 *
 * @param Model $Model
 */
	function setCachePath($Model) {
		$this->_initializeCache();
		$path = $this->_rootPath.Inflector::underscore($Model->alias);
		$Folder = new Folder($path, true, 0777);
		Cache::config($this->cacheConfig, array(
			'path' => $path
		));
	}

/**
 * Initializes the cache configuration for this instance of the data source
 *
 * @access protected
 */
	function _initializeCache() {
		if ($this->_rootPath !== null) {
			return;
		}
		$this->cacheConfig = $this->configKeyName.'SourceCache';
		Cache::config($this->cacheConfig, $this->_settings);
		$Folder = new Folder($this->_settings['path'], true, 0777);
		$this->_rootPath = $this->_settings['path'];
		if (substr($this->_rootPath, -1) != DS) {
			$this->_rootPath .= DS;
		}
	}

/**
 * Hashes a query into a unique string for the cache key
 *
 * @param array $query
 * @return string
 * @access protected
 */
	function _hash($query) {
		return md5(serialize($query['conditions']));
	}

}

?>